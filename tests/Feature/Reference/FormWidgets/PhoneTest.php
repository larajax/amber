<?php

namespace Tests\Feature\Reference\FormWidgets;

use App\Controllers\Reference\FormWidgetsController;
use App\Models\Sample;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Larajax\Ui\Widgets\Form;
use Tests\TestCase;

class PhoneTest extends TestCase
{
    use RefreshDatabase;

    /**
     * makePhoneForm creates a form with one phone field on the sample.
     *
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $formConfig
     */
    protected function makePhoneForm(Sample $sample, array $config = [], array $formConfig = []): Form
    {
        $form = Form::createIn(new FormWidgetsController, $formConfig + [
            'model' => $sample,
            'fields' => [
                'mobile' => $config + ['label' => 'Mobile', 'type' => 'phone'],
            ],
        ]);

        $form->bindToController();

        return $form;
    }

    /**
     * pageHtml renders the page for the reference sample.
     */
    protected function pageHtml(): string
    {
        Sample::factory()->create([
            'mobile' => '+61412345678',
            'office_phone' => '+6493030275',
            'support_phone' => '+14155552671',
        ]);

        return $this->get(route('reference.form-widgets.phone'))->assertOk()->getContent();
    }

    public function test_the_page_renders_every_example_beside_its_yaml(): void
    {
        $html = $this->pageHtml();

        foreach (['default', 'countries', 'read-only', 'preview'] as $example) {
            $this->assertStringContainsString("resources/ui/reference/form-widgets/phone/{$example}.yaml", $html);
        }

        // Three fields, and two numbers shown as links in preview mode
        $this->assertSame(5, substr_count($html, 'data-control="phone-input"'));
        $this->assertStringContainsString('data-request="onSaveExamples"', $html);
    }

    public function test_a_phone_field_shows_the_number_in_a_tel_input_and_posts_it_from_a_hidden_input(): void
    {
        $html = $this->pageHtml();

        $this->assertMatchesRegularExpression('/data-control="phone-input"\s+data-default-country="AU"\s*>\s*<div class="form-control form-adorn">/', $html);
        $this->assertMatchesRegularExpression('/<label for="(Form-exampleDefault-field-mobile)" class="form-label">.*?<input\s+type="tel"\s+id="\1"\s+value="\+61412345678"[^>]*class="form-ghost"\s+autocomplete="tel"\s+data-phone-input/s', $html);
        $this->assertMatchesRegularExpression('/<input\s+type="hidden"\s+name="mobile"\s+value="\+61412345678"\s+data-phone-value\s*\/>/', $html);
        $this->assertMatchesRegularExpression('/<button\s+type="button"\s+class="combobox-toggle phone-country"\s+data-bs-toggle="combobox"\s+data-bs-strategy="fixed"\s+aria-haspopup="listbox"\s+aria-expanded="false"\s+data-phone-country\s*>\s*<span class="flag-au" aria-hidden="true" data-phone-flag><\/span>/', $html);
        $this->assertMatchesRegularExpression('/<span class="combobox-value visually-hidden">\s*AU\s*<\/span>/', $html);
        $this->assertStringContainsString('<div class="menu phone-countries" role="listbox" data-phone-countries></div>', $html);
    }

    public function test_the_countries_and_preferred_countries_reach_the_picker(): void
    {
        $html = $this->pageHtml();

        $this->assertStringContainsString('data-countries="[&quot;AU&quot;,&quot;NZ&quot;,&quot;GB&quot;,&quot;US&quot;,&quot;CA&quot;]"', $html);
        $this->assertStringContainsString('data-preferred-countries="[&quot;AU&quot;,&quot;NZ&quot;]"', $html);
    }

    public function test_a_read_only_phone_locks_the_picker_and_the_input_but_still_posts(): void
    {
        $html = $this->pageHtml();

        preg_match('/id="Phone-exampleReadOnlySupportPhone-support_phone".*?data-phone-value\s*\/>/s', $html, $match);
        $this->assertNotEmpty($match);

        $this->assertMatchesRegularExpression('/data-phone-country\s+disabled/', $match[0]);
        $this->assertMatchesRegularExpression('/data-phone-input\s+readonly/', $match[0]);
        $this->assertMatchesRegularExpression('/name="support_phone"\s+value="\+14155552671"\s+data-phone-value\s*\/>/', $match[0]);
    }

    public function test_preview_mode_shows_a_link_that_calls_the_number(): void
    {
        $html = $this->pageHtml();

        $this->assertMatchesRegularExpression('/<a\s+href="tel:\+61412345678"\s+data-control="phone-input"\s+data-phone-value="\+61412345678"\s+data-default-country="AU"\s*>\s*\+61412345678\s*<\/a>/', $html);
        $this->assertStringContainsString('href="tel:+14155552671"', $html);
    }

    public function test_a_disabled_phone_posts_nothing(): void
    {
        $html = (string) $this->makePhoneForm(Sample::factory()->create(['mobile' => '+61412345678']), ['disabled' => true])->render();

        $this->assertMatchesRegularExpression('/data-phone-input\s+disabled/', $html);
        $this->assertMatchesRegularExpression('/name="mobile"\s+value="\+61412345678"\s+data-phone-value\s+disabled/', $html);
    }

    public function test_the_default_country_falls_back_to_the_region_of_the_app_locale(): void
    {
        $sample = Sample::factory()->create();

        app()->setLocale('en_NZ');
        $this->assertStringContainsString('data-default-country="NZ"', (string) $this->makePhoneForm($sample)->render());

        app()->setLocale('en');
        $this->assertStringContainsString('data-default-country=""', (string) $this->makePhoneForm($sample)->render());

        $this->assertStringContainsString('data-default-country="GB"', (string) $this->makePhoneForm($sample, ['defaultCountry' => 'gb'])->render());
    }

    public function test_saving_keeps_an_international_number_as_a_plus_and_digits(): void
    {
        $form = $this->makePhoneForm(new Sample);

        $form->setSaveDataOverride('mobile', ' +61 (412) 345-678 ');
        $this->assertSame('+61412345678', $form->getSaveData()['mobile']);

        $form->setSaveDataOverride('mobile', '');
        $this->assertNull($form->getSaveData()['mobile']);

        $form->setSaveDataOverride('mobile', '0412 345 678');
        $this->assertSame('0412 345 678', $form->getSaveData()['mobile']);
    }

    public function test_saving_the_page_stores_every_example_on_the_reference_sample(): void
    {
        $sample = Sample::factory()->create();

        $this->withHeaders([
            'X-AJAX-HANDLER' => 'onSaveExamples',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.form-widgets.phone'), [
            'mobile' => '+442079460958',
            'office_phone' => '',
            'support_phone' => '+14155552671',
        ])->assertOk();

        $sample->refresh();
        $this->assertSame('+442079460958', $sample->mobile);
        $this->assertNull($sample->office_phone);
        $this->assertSame('+14155552671', $sample->support_phone);
    }
}
