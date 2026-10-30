<?php

namespace Tests\Feature\Reference\FormWidgets;

use App\Controllers\Reference\FormWidgetsController;
use App\Models\Sample;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Widgets\Form;
use October\Rain\Exception\ForbiddenException;
use Tests\TestCase;

class RichEditorTest extends TestCase
{
    use RefreshDatabase;

    protected function makeEditorForm(Sample $sample, array $config = [], array $formConfig = []): Form
    {
        $form = Form::createIn(new FormWidgetsController, $formConfig + [
            'model' => $sample,
            'fields' => [
                'overview' => $config + ['label' => 'Overview', 'type' => 'richeditor'],
            ],
        ]);

        $form->bindToController();

        return $form;
    }

    /**
     * uploadToStory posts a file to the uploads example, which stores files on the public disk.
     */
    protected function uploadToStory(UploadedFile $file, string $type): TestResponse
    {
        Sample::factory()->create();

        return $this->withHeaders([
            'X-AJAX-HANDLER' => 'exampleUploadsStory::onUploadFile',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.form-widgets.rich-editor'), [
            'file_data' => $file,
            'file_type' => $type,
        ]);
    }

    public function test_the_page_renders_every_example_beside_its_yaml(): void
    {
        Sample::factory()->create([
            'overview' => '<p>Hello & welcome</p>',
            'press_release' => '<p>Safe</p><script>alert(1)</script>',
        ]);

        $html = $this->get(route('reference.form-widgets.rich-editor'))->assertOk()->getContent();

        foreach (['default', 'toolbar', 'full-toolbar', 'uploads', 'line-breaks', 'editor-options', 'size', 'read-only', 'preview'] as $example) {
            $this->assertStringContainsString("resources/ui/reference/form-widgets/rich-editor/{$example}.yaml", $html);
        }

        // Eight editors, while the preview example shows the cleaned HTML
        $this->assertSame(8, substr_count($html, 'data-control="richeditor"'));
        $this->assertStringContainsString('&lt;p&gt;Hello &amp; welcome&lt;/p&gt;</textarea>', $html);
        $this->assertStringContainsString('<p>Safe</p>', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);

        // Each example's options reach the control
        $this->assertStringContainsString('data-toolbar-buttons="bold,italic,underline,|,formatUL,formatOL,|,insertLink,|,undo,redo"', $html);
        $this->assertStringContainsString('data-toolbar-buttons="paragraphFormat,paragraphStyle,inlineStyle,fontFamily,fontSize,color,|,bold,', $html);
        $this->assertSame(1, substr_count($html, 'data-upload-handler='));
        $this->assertStringContainsString('data-upload-handler="exampleUploadsStory::onUploadFile"', $html);
        $this->assertStringContainsString('data-use-line-breaks="true"', $html);
        $this->assertStringContainsString('data-editor-options="{&quot;paragraphFormat&quot;:{&quot;N&quot;:&quot;Body&quot;', $html);
        $this->assertStringContainsString('&quot;imageDefaultAlign&quot;:&quot;left&quot;,&quot;imageResize&quot;:false}"', $html);
        $this->assertStringContainsString('class="field-richeditor size-small ', $html);
        $this->assertSame(1, substr_count($html, 'data-read-only="true"'));
    }

    public function test_saving_the_page_stores_every_example_on_the_reference_sample(): void
    {
        $sample = Sample::factory()->create(['press_release' => '<p>Kept</p>']);

        $this->withHeaders([
            'X-AJAX-HANDLER' => 'onSaveExamples',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.form-widgets.rich-editor'), [
            'overview' => '<p>Hello <strong>there</strong></p>',
            'key_points' => '<ul><li>One</li></ul>',
            'specifications' => '<h3>Light</h3>',
            'story' => '<p><img src="/storage/uploads/richeditor/a.png"></p>',
            'signature' => '<p>Mia<br>Lumen Works</p>',
            'article' => '<p class="oc-text-gray">Muted</p>',
            'teaser' => '<p>Short</p>',
            'disclaimer' => '<p>Unchanged</p>',
        ])->assertOk();

        $sample->refresh();
        $this->assertSame('<p>Hello <strong>there</strong></p>', $sample->overview);
        $this->assertSame('<ul><li>One</li></ul>', $sample->key_points);
        $this->assertSame('<h3>Light</h3>', $sample->specifications);
        $this->assertSame('<p><img src="/storage/uploads/richeditor/a.png"></p>', $sample->story);
        $this->assertSame('<p>Mia<br>Lumen Works</p>', $sample->signature);
        $this->assertSame('<p class="oc-text-gray">Muted</p>', $sample->article);
        $this->assertSame('<p>Short</p>', $sample->teaser);
        $this->assertSame('<p>Unchanged</p>', $sample->disclaimer);
        $this->assertSame('<p>Kept</p>', $sample->press_release);
    }

    public function test_the_field_renders_the_control_over_a_hidden_textarea(): void
    {
        $html = (string) $this->makeEditorForm(Sample::factory()->create(['overview' => '<p>Hello & welcome</p>']), ['placeholder' => 'Write a note'])->render();

        $this->assertStringContainsString('data-control="richeditor"', $html);
        $this->assertStringContainsString('class="field-richeditor size-large ', $html);
        $this->assertMatchesRegularExpression('/<textarea\s+data-richeditor-textarea\s+placeholder="Write a note"\s+name="overview"/', $html);
        $this->assertStringContainsString('&lt;p&gt;Hello &amp; welcome&lt;/p&gt;</textarea>', $html);
        $this->assertStringNotContainsString('data-toolbar-buttons', $html);
        $this->assertStringNotContainsString('data-read-only', $html);
    }

    public function test_toolbar_buttons_accept_a_list_or_a_pipe_separated_string(): void
    {
        $sample = Sample::factory()->create();

        $list = (string) $this->makeEditorForm($sample, ['toolbarButtons' => ['bold', 'italic', '|', 'html']])->render();
        $this->assertStringContainsString('data-toolbar-buttons="bold,italic,|,html"', $list);

        $string = (string) $this->makeEditorForm($sample, ['toolbarButtons' => 'bold|italic||html'])->render();
        $this->assertStringContainsString('data-toolbar-buttons="bold,italic,|,html"', $string);
    }

    public function test_options_render_as_control_attributes(): void
    {
        $html = (string) $this->makeEditorForm(Sample::factory()->create(), [
            'useLineBreaks' => true,
            'size' => 'huge',
            'editorOptions' => ['imageResize' => false, 'paragraphStyles' => ['lead' => 'Lead']],
        ])->render();

        $this->assertStringContainsString('class="field-richeditor size-huge ', $html);
        $this->assertStringContainsString('data-use-line-breaks="true"', $html);
        $this->assertStringContainsString('data-editor-options="{&quot;imageResize&quot;:false,&quot;paragraphStyles&quot;:{&quot;lead&quot;:&quot;Lead&quot;}}"', $html);
    }

    public function test_disabled_and_read_only_fields_render_a_read_only_editor(): void
    {
        $sample = Sample::factory()->create();

        $readOnly = (string) $this->makeEditorForm($sample, ['readOnly' => true])->render();
        $this->assertStringContainsString('data-read-only="true"', $readOnly);

        $disabled = (string) $this->makeEditorForm($sample, ['disabled' => true])->render();
        $this->assertStringContainsString('data-read-only="true"', $disabled);
    }

    public function test_preview_mode_renders_the_cleaned_html(): void
    {
        $sample = Sample::factory()->create(['overview' => '<p>Safe</p><script>alert(1)</script>']);

        $safe = (string) $this->makeEditorForm($sample, [], ['previewMode' => true])->render();
        $this->assertStringNotContainsString('data-control="richeditor"', $safe);
        $this->assertStringContainsString('<p>Safe</p>', $safe);
        $this->assertStringNotContainsString('<script>', $safe);

        $raw = (string) $this->makeEditorForm($sample, ['safeMode' => false], ['previewMode' => true])->render();
        $this->assertStringContainsString('<script>alert(1)</script>', $raw);
    }

    public function test_the_html_is_saved_as_submitted(): void
    {
        $widget = $this->makeEditorForm(Sample::factory()->create())->getFormWidget('overview');

        $this->assertSame('<p>Hello <strong>there</strong></p>', $widget->getSaveValue('<p>Hello <strong>there</strong></p>'));
    }

    public function test_the_field_passes_icon_pack_classes_and_translated_strings(): void
    {
        $html = (string) $this->makeEditorForm(Sample::factory()->create())->render();

        $this->assertStringContainsString('data-icons="{&quot;bold&quot;:&quot;ph ph-text-b&quot;,', $html);
        $this->assertStringContainsString('&quot;snippet&quot;:&quot;ph ph-newspaper&quot;}"', $html);
        $this->assertStringContainsString('data-lang="{&quot;toolbar&quot;:&quot;Editor toolbar&quot;,', $html);
        $this->assertStringContainsString('&quot;formatHeading1&quot;:&quot;Heading 1&quot;', $html);
    }

    public function test_the_built_in_upload_is_offered_only_with_an_upload_disk(): void
    {
        $sample = Sample::factory()->create();

        $this->assertStringNotContainsString('data-upload-handler', (string) $this->makeEditorForm($sample)->render());

        $enabled = (string) $this->makeEditorForm($sample, ['uploadDisk' => 'public'])->render();
        $this->assertStringContainsString('data-upload-handler="formOverview::onUploadFile"', $enabled);

        $readOnly = (string) $this->makeEditorForm($sample, ['uploadDisk' => 'public', 'readOnly' => true])->render();
        $this->assertStringNotContainsString('data-upload-handler', $readOnly);
    }

    public function test_the_upload_handler_refuses_without_an_upload_disk(): void
    {
        $widget = $this->makeEditorForm(Sample::factory()->create())->getFormWidget('overview');

        $this->expectException(ForbiddenException::class);
        $widget->onUploadFile();
    }

    public function test_uploaded_images_are_stored_and_their_address_returned(): void
    {
        Storage::fake('public');

        $response = $this->uploadToStory(UploadedFile::fake()->image('photo.png', 20, 20), 'image');
        $this->assertSame(200, $response->status(), $response->getContent());

        $url = $response->json('url');
        $this->assertMatchesRegularExpression('#^/storage/uploads/richeditor/\w{40}\.png$#', $url);
        Storage::disk('public')->assertExists(Str::after($url, '/storage/'));
    }

    public function test_file_links_accept_documents(): void
    {
        Storage::fake('public');

        $response = $this->uploadToStory(UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'), 'file');
        $this->assertSame(200, $response->status(), $response->getContent());

        $this->assertMatchesRegularExpression('#^/storage/uploads/richeditor/\w{40}\.pdf$#', $response->json('url'));
        $this->assertCount(1, Storage::disk('public')->allFiles('uploads/richeditor'));
    }

    public function test_images_refuse_documents(): void
    {
        Storage::fake('public');

        $response = $this->uploadToStory(UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'), 'image')->assertStatus(400);

        $this->assertStringContainsString('jpg, jpeg, png, gif, webp.', $response->json('__ajax.message'));
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_uploads_refuse_files_over_the_size_limit(): void
    {
        Storage::fake('public');

        $response = $this->uploadToStory(UploadedFile::fake()->create('manual.pdf', 3000, 'application/pdf'), 'file')->assertStatus(400);

        $this->assertStringContainsString('must not be greater than 2048 kilobytes', $response->json('__ajax.message'));
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_uploads_refuse_active_content(): void
    {
        Storage::fake('public');

        $response = $this->uploadToStory(UploadedFile::fake()->createWithContent('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'), 'file')
            ->assertStatus(400)
            ->assertJsonPath('__ajax.ok', false);

        $this->assertStringContainsString('must have one of the following extensions', $response->json('__ajax.message'));
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_uploads_check_the_file_content_as_well_as_its_name(): void
    {
        Storage::fake('public');

        $path = tempnam(sys_get_temp_dir(), 'richeditor');
        file_put_contents($path, '<!DOCTYPE html><html><body><script>alert(1)</script></body></html>');

        try {
            $response = $this->uploadToStory(new UploadedFile($path, 'report.pdf', 'application/pdf', null, true), 'file')->assertStatus(400);
        }
        finally {
            @unlink($path);
        }

        $this->assertStringContainsString('must be a file of type', $response->json('__ajax.message'));
        $this->assertSame([], Storage::disk('public')->allFiles());
    }
}
