<?php

namespace Tests\Feature\Reference\FormWidgets;

use App\Controllers\Reference\FormWidgetsController;
use App\Models\Sample;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Larajax\Ui\FormWidgets\CodeEditor;
use Larajax\Ui\Widgets\Form;
use Tests\TestCase;

class CodeEditorTest extends TestCase
{
    use RefreshDatabase;

    protected function makeCodeForm(Sample $sample, array $config = [], array $formConfig = []): Form
    {
        $form = Form::createIn(new FormWidgetsController, $formConfig + [
            'model' => $sample,
            'fields' => [
                'php_code' => $config + ['label' => 'PHP', 'type' => 'codeeditor'],
            ],
        ]);

        $form->bindToController();

        return $form;
    }

    public function test_the_page_renders_every_example_beside_its_yaml(): void
    {
        Sample::factory()->create([
            'php_code' => '<?php echo "a & b";',
            'template' => '<h1>{{ name }}</h1>',
            'embed_code' => '<script>alert(1)</script>',
        ]);

        $html = $this->get(route('reference.form-widgets.code-editor'))->assertOk()->getContent();

        foreach (['default', 'language', 'size', 'word-wrap', 'indentation', 'appearance', 'typing', 'read-only', 'preview'] as $example) {
            $this->assertStringContainsString("resources/ui/reference/form-widgets/code-editor/{$example}.yaml", $html);
        }

        // Eight editors, while the preview example shows its code as escaped text
        $this->assertSame(8, substr_count($html, 'data-control="codeeditor"'));
        $this->assertStringContainsString('&lt;?php echo &quot;a &amp; b&quot;;</textarea>', $html);
        $this->assertStringContainsString('<pre>&lt;script&gt;alert(1)&lt;/script&gt;</pre>', $html);
        $this->assertDoesNotMatchRegularExpression('/\sname="embed_code"/', $html);

        // Each example's options reach the control
        $this->assertStringContainsString('data-language="twig"', $html);
        $this->assertStringContainsString('class="field-codeeditor size-small ', $html);
        $this->assertStringContainsString('data-word-wrap="80"', $html);
        $this->assertStringContainsString('data-show-print-margin="true"', $html);
        $this->assertStringContainsString('data-tab-size="2"', $html);
        $this->assertStringContainsString('data-show-invisibles="true"', $html);
        $this->assertStringContainsString('data-code-folding="markbegin"', $html);
        $this->assertStringContainsString('data-font-size="14"', $html);
        $this->assertStringContainsString('data-margin="16"', $html);
        $this->assertStringContainsString('data-show-gutter="false"', $html);
        $this->assertStringContainsString('data-highlight-active-line="false"', $html);
        $this->assertStringContainsString('data-display-indent-guides="false"', $html);
        $this->assertStringContainsString('data-autocompletion="live"', $html);
        $this->assertStringContainsString('data-auto-close-tags="false"', $html);
        $this->assertStringContainsString('data-read-only="true"', $html);
        $this->assertStringContainsString('data-request="onSaveExamples"', $html);
    }

    public function test_saving_the_page_stores_every_example_on_the_reference_sample(): void
    {
        $sample = Sample::factory()->create(['embed_code' => '<p>Kept</p>']);

        $this->withHeaders([
            'X-AJAX-HANDLER' => 'onSaveExamples',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.form-widgets.code-editor'), [
            'php_code' => "<?php\n\nreturn [];",
            'template' => '<h1>{{ name }}</h1>',
            'css_code' => '.lamp { color: red; }',
            'html_code' => '<p>Hello</p>',
            'config_code' => "lamp:\n  name: Aurora",
            'script_code' => 'lamp.toggle();',
            'scss_code' => '.lamp { &:hover { color: red; } }',
            'generated_code' => '{"sku": "AUR-001"}',
        ])->assertOk();

        $sample->refresh();
        $this->assertSame("<?php\n\nreturn [];", $sample->php_code);
        $this->assertSame('<h1>{{ name }}</h1>', $sample->template);
        $this->assertSame('.lamp { color: red; }', $sample->css_code);
        $this->assertSame('<p>Hello</p>', $sample->html_code);
        $this->assertSame("lamp:\n  name: Aurora", $sample->config_code);
        $this->assertSame('lamp.toggle();', $sample->script_code);
        $this->assertSame('.lamp { &:hover { color: red; } }', $sample->scss_code);
        $this->assertSame('{"sku": "AUR-001"}', $sample->generated_code);

        // The preview example renders no input, so the page posts nothing for it
        $this->assertSame('<p>Kept</p>', $sample->embed_code);
    }

    public function test_the_field_renders_the_control_toolbar_and_textarea(): void
    {
        $html = (string) $this->makeCodeForm(Sample::factory()->create(['php_code' => '<?php echo "a & b";']))->render();

        $this->assertStringContainsString('data-control="codeeditor"', $html);
        $this->assertStringContainsString('class="field-codeeditor size-large ', $html);
        $this->assertStringContainsString('data-language="php"', $html);
        $this->assertStringContainsString('data-word-wrap="fluid"', $html);
        $this->assertStringContainsString('data-theme=""', $html);
        $this->assertStringContainsString('data-read-only="false"', $html);
        foreach (['undo', 'redo', 'search', 'replace', 'format', 'wrap', 'fullscreen'] as $command) {
            $this->assertStringContainsString('data-editor-command="'.$command.'"', $html);
        }
        $this->assertStringContainsString('title="Find (Ctrl+F)"', $html);
        $this->assertStringContainsString('editor-toolbar-icon-exit', $html);
        $this->assertMatchesRegularExpression('/data-editor-language>\s*php\s*<\/span>/', $html);
        $this->assertStringContainsString('&lt;?php echo &quot;a &amp; b&quot;;</textarea>', $html);
        $this->assertStringNotContainsString('data-vendor-path', $html);
    }

    public function test_options_render_as_control_attributes(): void
    {
        $html = (string) $this->makeCodeForm(Sample::factory()->create(), [
            'language' => 'markdown',
            'theme' => 'vs-dark',
            'fontSize' => 14,
            'tabSize' => 2,
            'showGutter' => false,
            'autocompletion' => 'live',
            'showPrintMargin' => true,
            'size' => 'giant',
        ])->render();

        $this->assertStringContainsString('class="field-codeeditor size-giant ', $html);
        $this->assertStringContainsString('data-language="markdown"', $html);
        $this->assertStringContainsString('data-theme="vs-dark"', $html);
        $this->assertStringContainsString('data-font-size="14"', $html);
        $this->assertStringContainsString('data-tab-size="2"', $html);
        $this->assertStringContainsString('data-show-gutter="false"', $html);
        $this->assertStringContainsString('data-autocompletion="live"', $html);
        $this->assertStringContainsString('data-show-print-margin="true"', $html);
    }

    public function test_boolean_word_wrap_values_are_normalized(): void
    {
        $sample = Sample::factory()->create();

        $on = (string) $this->makeCodeForm($sample, ['wordWrap' => true])->render();
        $this->assertStringContainsString('data-word-wrap="fluid"', $on);

        $off = (string) $this->makeCodeForm($sample, ['wordWrap' => false])->render();
        $this->assertStringContainsString('data-word-wrap="off"', $off);
    }

    public function test_disabled_and_read_only_fields_render_a_read_only_editor(): void
    {
        $sample = Sample::factory()->create();

        $readOnly = (string) $this->makeCodeForm($sample, ['readOnly' => true])->render();
        $this->assertStringContainsString('data-read-only="true"', $readOnly);

        $disabled = (string) $this->makeCodeForm($sample, ['disabled' => true])->render();
        $this->assertStringContainsString('data-read-only="true"', $disabled);
    }

    public function test_preview_mode_renders_the_escaped_code(): void
    {
        $sample = Sample::factory()->create(['php_code' => '<script>alert(1)</script>']);

        $html = (string) $this->makeCodeForm($sample, [], ['previewMode' => true])->render();

        $this->assertStringNotContainsString('data-control="codeeditor"', $html);
        $this->assertStringContainsString('<pre>&lt;script&gt;alert(1)&lt;/script&gt;</pre>', $html);
    }

    public function test_editor_preferences_apply_before_the_field_config(): void
    {
        $manager = app('system.widgets');
        $manager->listFormWidgets();
        $manager->registerFormWidget(PreferencedCodeEditor::class, 'preferencedcodeeditor');

        $sample = Sample::factory()->create();

        $preferred = (string) $this->makeCodeForm($sample, ['type' => 'preferencedcodeeditor'])->render();
        $this->assertStringContainsString('data-font-size="16"', $preferred);
        $this->assertStringContainsString('data-theme="hc-black"', $preferred);

        $configured = (string) $this->makeCodeForm($sample, ['type' => 'preferencedcodeeditor', 'fontSize' => 11])->render();
        $this->assertStringContainsString('data-font-size="11"', $configured);
    }

    public function test_the_code_is_saved_as_submitted(): void
    {
        $widget = $this->makeCodeForm(Sample::factory()->create())->getFormWidget('php_code');

        $this->assertSame("# Title\n\n<b>bold</b>", $widget->getSaveValue("# Title\n\n<b>bold</b>"));
    }
}

/**
 * PreferencedCodeEditor stands in for an editor that reads its defaults from user preferences.
 */
class PreferencedCodeEditor extends CodeEditor
{
    public function init()
    {
        $this->addViewPathFrom(CodeEditor::class);

        parent::init();
    }

    protected function applyEditorPreferences()
    {
        $this->fontSize = 16;
        $this->theme = 'hc-black';
    }
}
