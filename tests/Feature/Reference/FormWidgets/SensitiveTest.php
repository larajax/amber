<?php

namespace Tests\Feature\Reference\FormWidgets;

use App\Controllers\Reference\FormWidgetsController;
use App\Models\Sample;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Larajax\Ui\Widgets\Form;
use Tests\TestCase;

class SensitiveTest extends TestCase
{
    use RefreshDatabase;

    protected function makeSensitiveForm(Sample $sample, array $config = []): Form
    {
        $form = Form::createIn(new FormWidgetsController, [
            'model' => $sample,
            'fields' => [
                'api_key' => $config + ['label' => 'Secret', 'type' => 'sensitive'],
            ],
        ]);

        $form->bindToController();

        return $form;
    }

    /**
     * makeSecretSample creates the reference sample with a value in every sensitive example.
     */
    protected function makeSecretSample(): Sample
    {
        return Sample::factory()->create([
            'api_key' => 'sk_live_1',
            'webhook_secret' => 'whsec_2',
            'private_notes' => "Line one\nLine two",
            'license_key' => 'LICENSE-3',
            'access_token' => 'tok_4',
        ]);
    }

    public function test_the_page_renders_every_example_masked_beside_its_yaml(): void
    {
        $this->makeSecretSample();

        $html = $this->get(route('reference.form-widgets.sensitive'))->assertOk()->getContent();

        foreach (['default', 'allow-copy', 'textarea', 'read-only', 'tab-change'] as $example) {
            $this->assertStringContainsString("resources/ui/reference/form-widgets/sensitive/{$example}.yaml", $html);
        }

        $this->assertSame(5, substr_count($html, 'data-control="sensitive"'));
        foreach (['sk_live_1', 'whsec_2', 'Line one', 'LICENSE-3', 'tok_4'] as $secret) {
            $this->assertStringNotContainsString($secret, $html);
        }

        $this->assertStringContainsString('data-reveal-handler="exampleDefaultApiKey::onShowValue"', $html);
        $this->assertSame(2, substr_count($html, 'data-sensitive-copy'));
        $this->assertStringContainsString('data-display-mode="textarea"', $html);
        $this->assertMatchesRegularExpression('/name="license_key"[^>]*value="__hidden__"[^>]*disabled/s', $html);
        $this->assertSame(4, substr_count($html, 'data-hide-on-tab-change="true"'));
    }

    public function test_the_reveal_handler_returns_the_stored_value_on_the_page(): void
    {
        $this->makeSecretSample();

        $this->withHeaders([
            'X-AJAX-HANDLER' => 'exampleReadOnlyLicenseKey::onShowValue',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.form-widgets.sensitive'))
            ->assertOk()
            ->assertJsonPath('value', 'LICENSE-3');
    }

    public function test_saving_the_page_keeps_unrevealed_values_and_stores_new_ones(): void
    {
        $sample = $this->makeSecretSample();

        $this->withHeaders([
            'X-AJAX-HANDLER' => 'onSaveExamples',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.form-widgets.sensitive'), [
            'api_key' => '__hidden__',
            'webhook_secret' => 'whsec_new',
            'private_notes' => '__hidden__',
            'access_token' => '',
        ])->assertOk();

        $sample->refresh();
        $this->assertSame('sk_live_1', $sample->api_key);
        $this->assertSame('whsec_new', $sample->webhook_secret);
        $this->assertSame("Line one\nLine two", $sample->private_notes);
        $this->assertSame('LICENSE-3', $sample->license_key);
        $this->assertSame('', (string) $sample->access_token);
    }

    public function test_a_stored_value_renders_masked_with_a_reveal_handler(): void
    {
        $form = $this->makeSensitiveForm(Sample::factory()->create(['api_key' => 's3cret']), ['allowCopy' => true]);
        $html = (string) $form->render();

        $this->assertStringContainsString('data-control="sensitive"', $html);
        $this->assertStringContainsString('data-reveal-handler="formApiKey::onShowValue"', $html);
        $this->assertMatchesRegularExpression('/type="password"\s+name="api_key"[^>]*value="__hidden__"/', $html);
        $this->assertStringContainsString('data-sensitive-copy', $html);
        $this->assertStringContainsString('data-icon-show="ph ph-eye"', $html);
        $this->assertStringContainsString('data-icon-hide="ph ph-eye-slash"', $html);
        $this->assertStringNotContainsString('s3cret', $html);

        $this->assertSame(['value' => 's3cret'], $form->getFormWidget('api_key')->onShowValue());
    }

    public function test_the_placeholder_saves_the_stored_value_and_new_input_replaces_it(): void
    {
        $widget = $this->makeSensitiveForm(Sample::factory()->create(['api_key' => 's3cret']))->getFormWidget('api_key');

        $this->assertSame('s3cret', $widget->getSaveValue('__hidden__'));
        $this->assertSame('changed', $widget->getSaveValue('changed'));
    }

    public function test_a_custom_placeholder_stands_in_for_the_value(): void
    {
        $form = $this->makeSensitiveForm(Sample::factory()->create(['api_key' => 's3cret']), ['hiddenPlaceholder' => '********']);

        $this->assertMatchesRegularExpression('/name="api_key"[^>]*value="\*{8}"/', (string) $form->render());
        $this->assertSame('s3cret', $form->getFormWidget('api_key')->getSaveValue('********'));
    }

    public function test_an_empty_value_renders_without_the_placeholder(): void
    {
        $html = (string) $this->makeSensitiveForm(Sample::factory()->create(['api_key' => null]))->render();

        $this->assertMatchesRegularExpression('/name="api_key"[^>]*value=""/', $html);
        $this->assertStringNotContainsString('data-sensitive-copy', $html);
    }

    public function test_textarea_mode_shows_a_masked_facade(): void
    {
        $html = (string) $this->makeSensitiveForm(Sample::factory()->create(['api_key' => 's3cret']), ['mode' => 'textarea'])->render();

        $this->assertStringContainsString('data-display-mode="textarea"', $html);
        $this->assertStringContainsString('data-sensitive-facade', $html);
        $this->assertMatchesRegularExpression('/<textarea\s+name="api_key"/', $html);
        $this->assertStringContainsString('Reveal Contents', $html);
    }

    public function test_a_read_only_field_is_disabled(): void
    {
        $html = (string) $this->makeSensitiveForm(Sample::factory()->create(['api_key' => 's3cret']), ['readOnly' => true])->render();

        $this->assertMatchesRegularExpression('/name="api_key"[^>]*disabled/', $html);
    }

    public function test_hiding_on_tab_change_can_be_turned_off(): void
    {
        $html = (string) $this->makeSensitiveForm(Sample::factory()->create(['api_key' => 's3cret']), ['hideOnTabChange' => false])->render();

        $this->assertStringNotContainsString('data-hide-on-tab-change', $html);
    }
}
