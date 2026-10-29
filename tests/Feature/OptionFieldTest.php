<?php

namespace Tests\Feature;

use App\Controllers\KitchenSinkController;
use App\Models\Sample;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Larajax\Ui\Widgets\Form;
use Tests\TestCase;

class OptionFieldTest extends TestCase
{
    use RefreshDatabase;

    /**
     * renderField renders a form holding one field for the given sample.
     */
    protected function renderField(Sample $sample, string $name, array $config): string
    {
        $form = Form::createIn(new KitchenSinkController, [
            'model' => $sample,
            'fields' => [$name => $config],
        ]);

        $form->bindToController();

        return (string) $form->render();
    }

    public function test_a_balloon_selector_renders_focusable_radio_options_and_one_value_input(): void
    {
        $html = $this->renderField(Sample::factory()->make(['status' => 'active']), 'status', [
            'label' => 'Status',
            'type' => 'balloon-selector',
            'options' => ['draft' => 'Draft', 'active' => 'Active'],
        ]);

        $this->assertStringContainsString('data-control="balloon-selector"', $html);
        $this->assertStringContainsString('role="radiogroup"', $html);
        $this->assertMatchesRegularExpression('/data-value="active"\s+role="radio"\s+aria-checked="true"\s+tabindex="0"\s+class="active"/', $html);
        $this->assertMatchesRegularExpression('/data-value="draft"\s+role="radio"\s+aria-checked="false"\s+tabindex="0"/', $html);
        $this->assertMatchesRegularExpression('/<input\s+type="hidden"\s+name="status"\s+value="active"/', $html);
        $this->assertSame(1, substr_count($html, 'id="Form-field-status"'));
    }

    public function test_a_read_only_balloon_selector_is_locked_but_keeps_its_value(): void
    {
        $html = $this->renderField(Sample::factory()->make(['status' => 'draft']), 'status', [
            'label' => 'Status',
            'type' => 'balloon-selector',
            'readOnly' => true,
            'options' => ['draft' => 'Draft', 'active' => 'Active'],
        ]);

        $this->assertStringContainsString('control-balloon-selector control-disabled', $html);
        $this->assertStringContainsString('aria-disabled="true"', $html);
        $this->assertStringNotContainsString('tabindex="0"', $html);
        $this->assertMatchesRegularExpression('/name="status"\s+value="draft"/', $html);
    }

    public function test_a_dropdown_disables_a_disabled_option_unless_it_is_the_saved_value(): void
    {
        $field = ['label' => 'Level', 'type' => 'dropdown', 'emptyOption' => 'Pick a level'];

        $html = $this->renderField(Sample::factory()->make(['level' => 'basic']), 'level', $field);

        $this->assertMatchesRegularExpression('/<option\s+disabled\s+data-icon="ph ph-archive"\s+value="legacy"/', $html);
        $this->assertMatchesRegularExpression('/<option\s+selected="selected"\s+data-icon="ph ph-cube"\s+value="basic"/', $html);
        $this->assertStringContainsString('data-status="#3498db"', $html);

        $html = $this->renderField(Sample::factory()->make(['level' => 'legacy']), 'level', $field);

        $this->assertMatchesRegularExpression('/<option\s+selected="selected"\s+data-icon="ph ph-archive"\s+value="legacy"/', $html);
    }
}
