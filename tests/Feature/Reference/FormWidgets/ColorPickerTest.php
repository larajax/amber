<?php

namespace Tests\Feature\Reference\FormWidgets;

use App\Controllers\Reference\FormWidgetsController;
use App\Models\Sample;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Larajax\Ui\Widgets\Form;
use October\Rain\Exception\ApplicationException;
use Tests\TestCase;

class ColorPickerTest extends TestCase
{
    use RefreshDatabase;

    protected function makeColorForm(Sample $sample, array $config = [], array $formConfig = []): Form
    {
        $form = Form::createIn(new FormWidgetsController, $formConfig + [
            'model' => $sample,
            'fields' => [
                'color' => $config + ['label' => 'Color', 'type' => 'colorpicker'],
            ],
        ]);

        $form->bindToController();

        return $form;
    }

    public function test_the_page_renders_every_example_beside_its_yaml(): void
    {
        Sample::factory()->create(['color' => '#3498db', 'border_color' => '#bdc3c7']);

        $html = $this->get(route('reference.form-widgets.color-picker'))->assertOk()->getContent();

        foreach (['default', 'fixed-swatches', 'model-colors', 'alpha', 'input-only', 'read-only'] as $example) {
            $this->assertStringContainsString("resources/ui/reference/form-widgets/color-picker/{$example}.yaml", $html);
        }

        // Five pickers, while the read-only example shows its color without one
        $this->assertSame(5, substr_count($html, 'data-control="colorpicker"'));
        $this->assertStringContainsString('<div class="clr-field" style="color: #3498db">', $html);
        $this->assertStringContainsString('allowCustom: false', $html);
        $this->assertStringContainsString('data-swatches="[&quot;#0f766e&quot;,&quot;#14b8a6&quot;,&quot;#f59e0b&quot;,&quot;#be123c&quot;]"', $html);
        $this->assertStringContainsString('data-swatches="[]"', $html);
        $this->assertMatchesRegularExpression('/name="border_color"\s+value="#bdc3c7"[^>]*readonly/', $html);
        $this->assertStringContainsString('data-request="onSaveExamples"', $html);
    }

    public function test_saving_the_page_stores_every_example_on_the_reference_sample(): void
    {
        $sample = Sample::factory()->create();

        $this->withHeaders([
            'X-AJAX-HANDLER' => 'onSaveExamples',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.form-widgets.color-picker'), [
            'color' => '#e74c3c',
            'accent_color' => '1abc9c',
            'overlay_color' => '#3498db80',
            'brand_color' => '#be123c',
            'text_color' => '2c3e50',
            'border_color' => '#bdc3c7',
        ])->assertOk();

        $sample->refresh();
        $this->assertSame('#e74c3c', $sample->color);
        $this->assertSame('#1abc9c', $sample->accent_color);
        $this->assertSame('#3498db80', $sample->overlay_color);
        $this->assertSame('#be123c', $sample->brand_color);
        $this->assertSame('#2c3e50', $sample->text_color);
        $this->assertSame('#bdc3c7', $sample->border_color);
    }

    public function test_the_field_renders_an_input_with_the_preset_colors_as_swatches(): void
    {
        $html = (string) $this->makeColorForm(Sample::factory()->create(['color' => '#3498db']))->render();

        $this->assertStringContainsString('data-control="colorpicker"', $html);
        $this->assertStringContainsString('data-swatches="[&quot;#1abc9c&quot;,&quot;#16a085&quot;', $html);
        $this->assertStringContainsString('<div class="clr-field" style="color: #3498db">', $html);
        $this->assertMatchesRegularExpression('/name="color"\s+value="#3498db"[^>]*data-colorpicker-input/', $html);
        $this->assertStringNotContainsString('data-swatches-only', $html);
        $this->assertStringNotContainsString('data-show-alpha', $html);
    }

    public function test_options_render_as_control_attributes(): void
    {
        $html = (string) $this->makeColorForm(Sample::factory()->create(), [
            'availableColors' => ['#000000', '#ffffff'],
            'allowCustom' => false,
            'showAlpha' => true,
            'allowEmpty' => true,
        ])->render();

        $this->assertStringContainsString('data-swatches="[&quot;#000000&quot;,&quot;#ffffff&quot;]"', $html);
        $this->assertStringContainsString('data-swatches-only="true"', $html);
        $this->assertStringContainsString('data-show-alpha="true"', $html);
        $this->assertStringContainsString('data-allow-empty="true"', $html);
    }

    public function test_show_input_drops_the_swatches(): void
    {
        $html = (string) $this->makeColorForm(Sample::factory()->create(), ['showInput' => true])->render();

        $this->assertStringContainsString('data-swatches="[]"', $html);
    }

    public function test_available_colors_can_come_from_a_model_method(): void
    {
        $sample = new class extends Sample {
            protected $table = 'samples';

            public function getBrandColors(): array
            {
                return ['#ff0000', '#00ff00'];
            }
        };

        $html = (string) $this->makeColorForm($sample, ['availableColors' => 'getBrandColors'])->render();
        $this->assertStringContainsString('data-swatches="[&quot;#ff0000&quot;,&quot;#00ff00&quot;]"', $html);

        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage('must define a method missingColors()');
        (string) $this->makeColorForm($sample, ['availableColors' => 'missingColors'])->render();
    }

    public function test_locked_fields_render_without_the_picker(): void
    {
        $sample = Sample::factory()->create(['color' => '#3498db']);

        $readOnly = (string) $this->makeColorForm($sample, ['readOnly' => true])->render();
        $this->assertStringNotContainsString('data-control="colorpicker"', $readOnly);
        $this->assertStringNotContainsString('data-colorpicker-input', $readOnly);
        $this->assertMatchesRegularExpression('/name="color"[^>]*readonly/', $readOnly);

        $preview = (string) $this->makeColorForm($sample, [], ['previewMode' => true])->render();
        $this->assertStringNotContainsString('data-control="colorpicker"', $preview);
        $this->assertMatchesRegularExpression('/name="color"[^>]*disabled/', $preview);
        $this->assertStringContainsString('style="color: #3498db"', $preview);
    }

    public function test_saved_values_are_normalized_to_hex(): void
    {
        $widget = $this->makeColorForm(Sample::factory()->create())->getFormWidget('color');

        $this->assertNull($widget->getSaveValue(''));
        $this->assertSame('#3498db', $widget->getSaveValue('3498db'));
        $this->assertSame('#3498db80', $widget->getSaveValue('#3498db80'));
    }
}
