<?php

namespace Tests\Feature\Reference\FormWidgets;

use App\Controllers\Reference\FormWidgetsController;
use App\Models\Sample;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Larajax\Ui\Widgets\Form;
use Tests\TestCase;

class MarkdownEditorTest extends TestCase
{
    use RefreshDatabase;

    protected function makeMarkdownForm(Sample $sample, array $config = [], array $formConfig = []): Form
    {
        $form = Form::createIn(new FormWidgetsController, $formConfig + [
            'model' => $sample,
            'fields' => [
                'readme' => $config + ['label' => 'Readme', 'type' => 'markdown'],
            ],
        ]);

        $form->bindToController();

        return $form;
    }

    protected function postRequest(array $data): void
    {
        $this->app->instance('request', Request::create('/reference/form-widgets/markdown-editor', 'POST', $data));
        Facade::clearResolvedInstance('request');
    }

    public function test_the_page_renders_every_example_beside_its_yaml(): void
    {
        Sample::factory()->create([
            'readme' => '**Bold** & <b>tag</b>',
            'announcement' => '<iframe srcdoc="x"></iframe>',
            'terms' => '# Terms',
            'warranty' => '# Warranty',
            'care_guide' => "# Care\n\n<script>alert(1)</script>",
        ]);

        $html = $this->get(route('reference.form-widgets.markdown-editor'))->assertOk()->getContent();

        foreach (['default', 'side-by-side', 'size', 'safe-mode', 'read-only', 'disabled', 'preview'] as $example) {
            $this->assertStringContainsString("resources/ui/reference/form-widgets/markdown-editor/{$example}.yaml", $html);
        }

        // Four editors, while read-only, disabled and preview show the rendered Markdown
        $this->assertSame(4, substr_count($html, 'data-control="markdowneditor"'));
        $this->assertStringContainsString('placeholder="Describe the product in Markdown"', $html);
        $this->assertStringContainsString('>**Bold** &amp; &lt;b&gt;tag&lt;/b&gt;</textarea>', $html);
        $this->assertSame(1, substr_count($html, 'data-view-mode="split"'));
        $this->assertStringContainsString('class="field-markdowneditor size-small ', $html);
        $this->assertSame(1, substr_count($html, 'data-safe-mode="false"'));
        $this->assertStringContainsString('<h1>Terms</h1>', $html);
        $this->assertStringContainsString(' hidden># Terms</textarea>', $html);
        $this->assertStringContainsString('<h1>Warranty</h1>', $html);
        $this->assertDoesNotMatchRegularExpression('/<textarea\s+name="warranty"/', $html);
        $this->assertStringContainsString('<h1>Care</h1>', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    public function test_saving_the_page_stores_every_example_on_the_reference_sample(): void
    {
        $sample = Sample::factory()->create(['warranty' => 'Five years']);

        $this->withHeaders([
            'X-AJAX-HANDLER' => 'onSaveExamples',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.form-widgets.markdown-editor'), [
            'readme' => "# Aurora\n\n* Warm",
            'changelog' => "## 1.3.0\n\n- New finish",
            'faq' => '**Dimmable?** Yes.',
            'announcement' => '<mark>Soon</mark>',
            'terms' => 'Ships in two days.',
            'warranty' => 'Forever',
        ])->assertOk();

        $sample->refresh();
        $this->assertSame("# Aurora\n\n* Warm", $sample->readme);
        $this->assertSame("## 1.3.0\n\n- New finish", $sample->changelog);
        $this->assertSame('**Dimmable?** Yes.', $sample->faq);
        $this->assertSame('<mark>Soon</mark>', $sample->announcement);

        // The read-only example posts its hidden value, while the disabled one is never saved
        $this->assertSame('Ships in two days.', $sample->terms);
        $this->assertSame('Five years', $sample->warranty);
    }

    public function test_the_refresh_handler_renders_the_posted_markdown_on_the_page(): void
    {
        Sample::factory()->create();

        $response = $this->withHeaders([
            'X-AJAX-HANDLER' => 'exampleDefaultReadme::onRefresh',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.form-widgets.markdown-editor'), [
            'readme' => "# Title\n\n**Bold**<script>alert(1)</script>",
        ]);

        $response->assertOk();
        $this->assertStringContainsString('<h1>Title</h1>', $response->json('preview'));
        $this->assertStringContainsString('<strong>Bold</strong>', $response->json('preview'));
        $this->assertStringNotContainsString('<script>', $response->json('preview'));
    }

    public function test_the_field_renders_the_control_over_the_textarea(): void
    {
        $html = (string) $this->makeMarkdownForm(Sample::factory()->create(['readme' => '**Bold** & <b>tag</b>']), ['placeholder' => 'Describe the group'])->render();

        $this->assertStringContainsString('data-control="markdowneditor"', $html);
        $this->assertStringContainsString('class="field-markdowneditor size-large ', $html);
        $this->assertStringContainsString('data-view-mode="tab"', $html);
        $this->assertStringContainsString('data-safe-mode="true"', $html);
        $this->assertStringContainsString('placeholder="Describe the group"', $html);
        $this->assertStringContainsString('data-size="large"', $html);
        $this->assertStringContainsString('data-toolbar-buttons="[{&quot;name&quot;:&quot;bold&quot;,&quot;title&quot;:&quot;Bold&quot;', $html);
        $this->assertStringContainsString('&lt;i class=\&quot;ph ph-text-b\&quot;', $html);
        $this->assertStringContainsString('&quot;name&quot;:&quot;code&quot;', $html);
        $this->assertStringContainsString('&quot;|&quot;', $html);
        $this->assertStringContainsString('>**Bold** &amp; &lt;b&gt;tag&lt;/b&gt;</textarea>', $html);
    }

    public function test_side_by_side_starts_in_split_mode(): void
    {
        $sample = Sample::factory()->create();

        $sideBySide = (string) $this->makeMarkdownForm($sample, ['sideBySide' => true])->render();
        $this->assertStringContainsString('data-view-mode="split"', $sideBySide);

        $mode = (string) $this->makeMarkdownForm($sample, ['mode' => 'split'])->render();
        $this->assertStringContainsString('data-view-mode="split"', $mode);
    }

    public function test_read_only_fields_render_the_markdown_and_keep_the_value(): void
    {
        $sample = Sample::factory()->create(['readme' => '# Title']);

        $html = (string) $this->makeMarkdownForm($sample, ['readOnly' => true])->render();

        $this->assertStringNotContainsString('data-control="markdowneditor"', $html);
        $this->assertStringContainsString('<h1>Title</h1>', $html);
        $this->assertStringContainsString('<textarea name="readme"', $html);
        $this->assertStringContainsString(' hidden># Title</textarea>', $html);
    }

    public function test_disabled_fields_render_the_markdown_without_a_value(): void
    {
        $sample = Sample::factory()->create(['readme' => '# Title']);

        $html = (string) $this->makeMarkdownForm($sample, ['disabled' => true])->render();

        $this->assertStringContainsString('<h1>Title</h1>', $html);
        $this->assertStringNotContainsString('<textarea', $html);
    }

    public function test_preview_mode_renders_the_markdown(): void
    {
        $sample = Sample::factory()->create(['readme' => "# Title\n\n<script>alert(1)</script>"]);

        $html = (string) $this->makeMarkdownForm($sample, [], ['previewMode' => true])->render();

        $this->assertStringNotContainsString('data-control="markdowneditor"', $html);
        $this->assertStringContainsString('<h1>Title</h1>', $html);
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function test_the_refresh_handler_renders_the_posted_markdown_cleaned(): void
    {
        $widget = $this->makeMarkdownForm(Sample::factory()->create())->getFormWidget('readme');

        $this->postRequest(['readme' => "# Title\n\n**Bold**<script>alert(1)</script>"]);
        $preview = $widget->onRefresh()['preview'];

        $this->assertStringContainsString('<h1>Title</h1>', $preview);
        $this->assertStringContainsString('<strong>Bold</strong>', $preview);
        $this->assertStringNotContainsString('<script>', $preview);
    }

    public function test_the_refresh_handler_keeps_html_without_safe_mode(): void
    {
        $widget = $this->makeMarkdownForm(Sample::factory()->create(), ['safeMode' => false])->getFormWidget('readme');

        $this->postRequest(['readme' => '<span class="note">Note</span>']);

        $this->assertStringContainsString('<span class="note">Note</span>', $widget->onRefresh()['preview']);
    }

    public function test_the_markdown_is_saved_as_submitted(): void
    {
        $widget = $this->makeMarkdownForm(Sample::factory()->create())->getFormWidget('readme');

        $this->assertSame("# Title\n\n* One", $widget->getSaveValue("# Title\n\n* One"));
    }
}
