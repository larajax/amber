<?php

namespace Tests\Feature\Reference\FormWidgets;

use App\Controllers\Reference\FormWidgetsController;
use App\Models\File;
use App\Models\Sample;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Widgets\Form;
use Tests\TestCase;

class FileUploadTest extends TestCase
{
    use RefreshDatabase;

    /**
     * setUp stores the uploads on a fake public disk.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    /**
     * ajax posts one AJAX handler request to the file upload page.
     */
    protected function ajax(string $handler, array $data = []): TestResponse
    {
        // Laravel keeps the route's controller across a test's requests, and the controller keeps its first AJAX handler
        app('router')->getRoutes()->getByName('reference.form-widgets.file-upload')->flushController();

        // Upload fields register with the request's held uploads, which a test's requests would otherwise share
        app()->forgetInstance('larajax.ui.uploads');

        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.form-widgets.file-upload'), $data);
    }

    /**
     * attachFiles stores files on a sample relation directly, as images when the name ends in png.
     *
     * @param  array<int, string>  $names
     * @return array<int, int>
     */
    protected function attachFiles(Sample $sample, string $relation, array $names): array
    {
        return array_map(function (string $name) use ($sample, $relation) {
            $upload = str_ends_with($name, '.png')
                ? UploadedFile::fake()->image($name, 40, 40)
                : UploadedFile::fake()->create($name, 1);

            $file = $sample->{$relation}()->make()->fromPost($upload);
            $file->save();

            return $file->id;
        }, $names);
    }

    public function test_the_page_renders_every_example_beside_its_yaml(): void
    {
        Sample::factory()->create();

        $html = $this->get(route('reference.form-widgets.file-upload'))->assertOk()->getContent();

        foreach (['image-single', 'image-multi', 'image-crop', 'image-multi-crop', 'file-single', 'file-multi', 'private', 'attach-on-upload', 'preview'] as $example) {
            $this->assertStringContainsString("resources/ui/reference/form-widgets/file-upload/{$example}.yaml", $html);
        }

        $this->assertMatchesRegularExpression('/class="field-fileupload is-image is-single\s[^"]*"\s+data-control="fileupload"/', $html);
        $this->assertMatchesRegularExpression('/class="field-fileupload size-large is-image is-multi is-grid is-sortable[^"]*"\s+data-control="fileupload"/', $html);
        $this->assertMatchesRegularExpression('/class="field-fileupload is-file is-single\s[^"]*"\s+data-control="fileupload"/', $html);
        $this->assertMatchesRegularExpression('/class="field-fileupload size-small is-file is-multi is-sortable[^"]*"\s+data-control="fileupload"/', $html);
        $this->assertStringContainsString('data-thumbnail-width="260"', $html);
        $this->assertStringContainsString('data-thumbnail-height="160"', $html);
        $this->assertStringContainsString('data-file-types=".jpg,.jpeg,.png,.gif,.webp"', $html);
        $this->assertStringContainsString('data-file-types=".pdf"', $html);
        $this->assertStringContainsString('data-max-files="6"', $html);
        $this->assertStringContainsString('data-max-filesize="2"', $html);
        $this->assertStringContainsString('data-messages="{&quot;maxFiles&quot;', $html);
        $this->assertStringContainsString('data-sort-handler="exampleFileMultiAttachments::onSortAttachments"', $html);
        $this->assertStringContainsString('There are no files uploaded.', $html);
        $this->assertMatchesRegularExpression('/name="_session_key" value="[^"]{40}"/', $html);
    }

    /**
     * uploadBanner uploads a fake image of the given size to the crop example's banner field.
     */
    protected function uploadBanner(int $width, int $height): TestResponse
    {
        return $this->ajax('exampleImageCropBanner::onUpload', [
            'file_data' => UploadedFile::fake()->image('banner.png', $width, $height),
            '_session_key' => 'session-1',
        ]);
    }

    public function test_the_crop_example_gives_the_field_its_crop_size_and_popup(): void
    {
        Sample::factory()->create();

        $html = $this->get(route('reference.form-widgets.file-upload'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/data-crop-width="1200"\s+data-crop-height="500"\s+data-crop-template="#([^"]+)"/', $html);
        preg_match('/data-crop-template="#([^"]+)"/', $html, $matches);
        $this->assertStringContainsString('<script type="text/template" id="'.$matches[1].'">', $html);

        // The parts of the popup the cropper fills in
        foreach (['data-cropper-stage', 'data-cropper-zoom', 'data-cropper-output', 'data-cropper-small', 'data-cropper-animated', 'data-cropper-apply'] as $part) {
            $this->assertStringContainsString($part, $html);
        }
        $this->assertStringContainsString('smaller than 600 × 250 pixels', $html);

        // Only the banner and the photos crop, so only they have a size and a popup
        $this->assertSame(2, substr_count($html, 'data-crop-width='));
        $this->assertSame(2, substr_count($html, 'class="image-cropper-popup"'));

        // With no size of its own, the preview takes the crop's shape, its longer side grown from 190
        $this->assertMatchesRegularExpression('/data-thumbnail-width="456"\s+data-thumbnail-height="190"[^>]*data-crop-width="1200"/', $html);
        $this->assertStringContainsString('style="--fileupload-image-ratio: 2.4"', $html);
    }

    public function test_the_multiple_crop_example_gives_the_gallery_its_crop_size_and_popup(): void
    {
        Sample::factory()->create();

        $html = $this->get(route('reference.form-widgets.file-upload'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/class="field-fileupload size-large is-image is-multi is-grid is-sortable[^"]*"\s+data-control="fileupload"[^>]*data-crop-width="900"\s+data-crop-height="1200"\s+data-crop-template="#([^"]+)"\s+data-max-files="4"/', $html);
        preg_match('/data-crop-width="900"\s+data-crop-height="1200"\s+data-crop-template="#([^"]+)"/', $html, $matches);
        $this->assertStringContainsString('<script type="text/template" id="'.$matches[1].'">', $html);
        $this->assertStringContainsString('smaller than 450 × 600 pixels', $html);

        // A portrait crop keeps the width and grows the preview's height
        $this->assertStringContainsString('style="--fileupload-image-ratio: 0.751"', $html);
    }

    public function test_photos_in_the_crop_shape_upload_and_save_in_order(): void
    {
        $sample = Sample::factory()->create();

        foreach (['first.png' => [900, 1200], 'second.png' => [450, 600], 'third.png' => [1800, 2400]] as $name => [$width, $height]) {
            $response = $this->ajax('exampleImageMultiCropPhotos::onUpload', [
                'file_data' => UploadedFile::fake()->image($name, $width, $height),
                '_session_key' => 'session-1',
            ]);
            $this->assertSame(200, $response->status(), "{$name}: ".$response->getContent());
        }

        $this->assertSame(0, $sample->photos()->count());

        $this->ajax('onSaveExamples', ['_session_key' => 'session-1'])->assertOk();

        $this->assertSame(['first.png', 'second.png', 'third.png'], $sample->fresh()->photos->pluck('file_name')->all());
    }

    public function test_a_photo_of_another_shape_is_refused_while_the_others_upload(): void
    {
        Sample::factory()->create();

        $this->ajax('exampleImageMultiCropPhotos::onUpload', [
            'file_data' => UploadedFile::fake()->image('portrait.png', 900, 1200),
            '_session_key' => 'session-1',
        ])->assertOk();

        $response = $this->ajax('exampleImageMultiCropPhotos::onUpload', [
            'file_data' => UploadedFile::fake()->image('landscape.png', 1600, 900),
            '_session_key' => 'session-1',
        ]);

        $response->assertStatus(400);
        $this->assertStringContainsString('The image must be cropped to the shape of 900 x 1200 pixels.', $response->getContent());
        $this->assertSame(['portrait.png'], File::pluck('file_name')->all());
    }

    public function test_a_crop_shapes_the_preview_unless_the_field_sets_its_size(): void
    {
        $form = Form::createIn(new FormWidgetsController, [
            'model' => new Sample,
            'fields' => [
                'cover' => ['label' => 'Cover', 'type' => 'fileupload', 'mode' => 'image', 'cropWidth' => 500, 'cropHeight' => 1000],
                'banner' => ['label' => 'Banner', 'type' => 'fileupload', 'mode' => 'image', 'cropWidth' => 1200, 'cropHeight' => 500, 'imageWidth' => 300, 'imageHeight' => 300],
            ],
        ]);
        $form->bindToController();

        $html = (string) $form->render();

        // A tall crop grows the height instead
        $this->assertMatchesRegularExpression('/data-thumbnail-width="190"\s+data-thumbnail-height="380"[^>]*data-crop-width="500"/', $html);
        $this->assertStringContainsString('style="--fileupload-image-ratio: 0.5"', $html);

        // Sizes the field sets win over the crop's shape
        $this->assertMatchesRegularExpression('/data-thumbnail-width="300"\s+data-thumbnail-height="300"[^>]*data-crop-width="1200"/', $html);
        $this->assertStringContainsString('style="--fileupload-image-ratio: 1"', $html);
    }

    public function test_a_crop_size_is_left_out_of_preview_mode_and_file_mode(): void
    {
        // A preview field with no file shows only a note, so the banner needs one to show the field
        $sample = Sample::factory()->create();
        $this->attachFiles($sample, 'banner', ['banner.png']);

        $form = Form::createIn(new FormWidgetsController, [
            'model' => $sample,
            'fields' => [
                'banner' => ['label' => 'Banner', 'type' => 'fileupload', 'mode' => 'image', 'cropWidth' => 1200, 'cropHeight' => 500, 'disabled' => true],
                'brochure' => ['label' => 'Brochure', 'type' => 'fileupload', 'mode' => 'file', 'cropWidth' => 1200, 'cropHeight' => 500],
            ],
        ]);
        $form->bindToController();

        $html = (string) $form->render();

        $this->assertMatchesRegularExpression('/class="field-fileupload is-image is-single is-populated is-preview"/', $html);
        $this->assertMatchesRegularExpression('/class="field-fileupload is-file is-single\s[^"]*"\s+data-control="fileupload"/', $html);
        $this->assertStringNotContainsString('data-crop-width', $html);
        $this->assertStringNotContainsString('image-cropper-popup', $html);
    }

    public function test_an_image_in_the_crop_shape_uploads_and_saves_as_the_banner(): void
    {
        $sample = Sample::factory()->create();

        // The server checks only the shape, since the browser scales a larger image down
        foreach ([[1200, 500], [600, 250], [2400, 1000]] as [$width, $height]) {
            $response = $this->uploadBanner($width, $height);
            $this->assertSame(200, $response->status(), "{$width} x {$height}: ".$response->getContent());
        }

        $this->assertSame(3, File::count());

        $this->ajax('onSaveExamples', ['_session_key' => 'session-1'])->assertOk();

        $banner = $sample->fresh()->banner;
        $this->assertSame('banner.png', $banner->file_name);
        $this->assertSame('banner', $banner->field);
    }

    public function test_an_image_of_another_shape_is_refused(): void
    {
        Sample::factory()->create();

        $response = $this->uploadBanner(1200, 900);

        $response->assertStatus(400);
        $this->assertStringContainsString('The image must be cropped to the shape of 1200 x 500 pixels.', $response->getContent());
        $this->assertSame(0, File::count());
    }

    public function test_a_shape_within_one_percent_or_a_pixel_passes(): void
    {
        Sample::factory()->create();

        // One percent of 500 is 5 pixels
        $this->uploadBanner(1200, 505)->assertOk();
        $this->uploadBanner(1200, 506)->assertStatus(400);

        // A small image may be a pixel out, where one percent is less than a pixel
        $this->uploadBanner(24, 11)->assertOk();
        $this->uploadBanner(24, 12)->assertStatus(400);

        $this->assertSame(2, File::count());
    }

    public function test_the_shape_on_its_side_passes(): void
    {
        Sample::factory()->create();

        // A photo turned by its Exif data reads as its stored size
        $this->uploadBanner(500, 1200)->assertOk();

        $this->assertSame(1, File::count());
    }

    public function test_preview_mode_shows_the_files_without_the_toolbar(): void
    {
        $sample = Sample::factory()->create();
        $this->attachFiles($sample, 'gallery', ['photo.png']);

        $html = $this->get(route('reference.form-widgets.file-upload'))->assertOk()->getContent();

        // A preview field cannot sort, so it leaves out is-sortable
        $this->assertMatchesRegularExpression('/class="field-fileupload size-large is-image is-multi is-grid\s+is-populated is-preview"/', $html);
        $this->assertSame(2, substr_count($html, 'data-name="photo.png"'));
        $this->assertStringNotContainsString('There are no files uploaded.', $html);
    }

    public function test_a_field_outside_its_context_is_left_out(): void
    {
        $form = Form::createIn(new FormWidgetsController, [
            'model' => new Sample,
            'context' => 'create',
            'fields' => [
                'gallery' => ['label' => 'Gallery', 'type' => 'fileupload', 'context' => 'update'],
            ],
        ]);
        $form->bindToController();

        $this->assertStringNotContainsString('data-control="fileupload"', (string) $form->render());
    }

    public function test_an_upload_waits_for_the_save_by_default(): void
    {
        $sample = Sample::factory()->create();

        $response = $this->ajax('exampleImageMultiGallery::onUpload', [
            'file_data' => UploadedFile::fake()->image('photo.png', 200, 100),
            '_session_key' => 'session-1',
        ]);
        $this->assertSame(200, $response->status(), $response->getContent());

        $this->assertSame(0, $sample->gallery()->count());

        // The upload is stored for the gallery field, without a record to belong to until the page saves
        $photo = File::sole();
        $this->assertSame('photo.png', $photo->file_name);
        $this->assertSame('gallery', $photo->field);
        $this->assertNull($photo->attachment_id);
        $this->assertTrue($photo->is_public);
        $this->assertSame($photo->id, $response->json('id'));
        $this->assertStringContainsString('/storage/uploads/public/', $response->json('path'));
        $this->assertSame($response->json('path'), $response->json('thumb'));
        Storage::disk('public')->assertExists($photo->getDiskPath());
    }

    public function test_saving_the_page_commits_the_uploads_held_against_the_session_key(): void
    {
        $sample = Sample::factory()->create();

        $this->ajax('exampleImageMultiGallery::onUpload', [
            'file_data' => UploadedFile::fake()->image('photo.png', 40, 40),
            '_session_key' => 'session-1',
        ])->assertOk();

        $this->ajax('onSaveExamples', ['_session_key' => 'session-1'])->assertOk();

        $this->assertSame(['photo.png'], $sample->fresh()->gallery->pluck('file_name')->all());
    }

    public function test_uploads_held_against_another_session_key_stay_unattached(): void
    {
        $sample = Sample::factory()->create();

        $this->ajax('exampleImageMultiGallery::onUpload', [
            'file_data' => UploadedFile::fake()->image('photo.png', 40, 40),
            '_session_key' => 'session-1',
        ])->assertOk();

        $this->ajax('onSaveExamples', ['_session_key' => 'session-2'])->assertOk();

        $this->assertSame(0, $sample->gallery()->count());
        $this->assertNull(File::sole()->attachment_id);
    }

    public function test_attach_on_upload_attaches_the_file_straight_away(): void
    {
        $sample = Sample::factory()->create();

        $response = $this->ajax('exampleAttachOnUploadDocuments::onUpload', [
            'file_data' => UploadedFile::fake()->create('notes.txt', 1, 'text/plain'),
            '_session_key' => 'session-1',
        ]);
        $this->assertSame(200, $response->status(), $response->getContent());

        $this->assertSame(['notes.txt'], $sample->fresh()->documents->pluck('file_name')->all());
    }

    public function test_a_private_upload_is_stored_in_the_protected_folder(): void
    {
        Sample::factory()->create();

        $response = $this->ajax('examplePrivateContract::onUpload', [
            'file_data' => UploadedFile::fake()->create('contract.pdf', 1, 'application/pdf'),
            '_session_key' => 'session-1',
        ]);
        $this->assertSame(200, $response->status(), $response->getContent());

        $contract = File::sole();
        $this->assertSame('contract.pdf', $contract->file_name);
        $this->assertFalse($contract->is_public);
        $this->assertStringContainsString('/storage/uploads/protected/', $response->json('path'));
        $this->assertStringStartsWith('uploads/protected/', $contract->getDiskPath());
        Storage::disk('public')->assertExists($contract->getDiskPath());
    }

    public function test_images_refuse_other_file_types(): void
    {
        Sample::factory()->create();

        $this->ajax('exampleImageMultiGallery::onUpload', [
            'file_data' => UploadedFile::fake()->create('notes.txt', 1, 'text/plain'),
        ])->assertStatus(400);

        $this->assertSame(0, File::count());
    }

    public function test_the_brochure_refuses_files_that_are_not_pdf(): void
    {
        Sample::factory()->create();

        $this->ajax('exampleFileSingleBrochure::onUpload', [
            'file_data' => UploadedFile::fake()->create('brochure.txt', 1, 'text/plain'),
        ])->assertStatus(400);

        $this->assertSame(0, File::count());
    }

    public function test_sorting_orders_the_attachments(): void
    {
        $sample = Sample::factory()->create();
        [$first, $second, $third] = $this->attachFiles($sample, 'attachments', ['agenda.txt', 'notes.txt', 'minutes.txt']);

        $sorted = $this->ajax('exampleFileMultiAttachments::onSortAttachments', [
            'sortOrder' => [$third => 1, $first => 2, $second => 3],
        ]);
        $this->assertSame(200, $sorted->status(), $sorted->getContent());

        $this->assertSame(['minutes.txt', 'agenda.txt', 'notes.txt'], $sample->fresh()->attachments->pluck('file_name')->all());
    }

    public function test_removing_an_attachment_waits_for_the_save(): void
    {
        $sample = Sample::factory()->create();
        [$first] = $this->attachFiles($sample, 'attachments', ['agenda.txt', 'notes.txt']);

        $this->ajax('exampleFileMultiAttachments::onRemoveAttachment', ['file_id' => $first, '_session_key' => 'session-1'])->assertOk();

        $this->assertSame(['agenda.txt', 'notes.txt'], $sample->fresh()->attachments->pluck('file_name')->all());
        $this->assertNotNull(File::find($first));
    }

    public function test_saving_the_page_commits_a_removed_attachment(): void
    {
        $sample = Sample::factory()->create();
        [$first] = $this->attachFiles($sample, 'attachments', ['agenda.txt', 'notes.txt']);
        $agenda = File::find($first);

        $this->ajax('exampleFileMultiAttachments::onRemoveAttachment', ['file_id' => $first, '_session_key' => 'session-1'])->assertOk();
        $this->ajax('onSaveExamples', ['_session_key' => 'session-1'])->assertOk();

        $this->assertSame(['notes.txt'], $sample->fresh()->attachments->pluck('file_name')->all());
        $this->assertNull(File::find($first));
        Storage::disk('public')->assertMissing($agenda->getDiskPath());
    }

    public function test_removing_a_held_upload_deletes_it_straight_away(): void
    {
        Sample::factory()->create();

        $fileId = $this->ajax('exampleImageMultiGallery::onUpload', [
            'file_data' => UploadedFile::fake()->image('photo.png', 40, 40),
            '_session_key' => 'session-1',
        ])->assertOk()->json('id');
        $diskPath = File::findOrFail($fileId)->getDiskPath();

        $this->ajax('exampleImageMultiGallery::onRemoveAttachment', ['file_id' => $fileId, '_session_key' => 'session-1'])->assertOk();

        $this->assertSame(0, File::count());
        Storage::disk('public')->assertMissing($diskPath);
    }

    public function test_preview_mode_refuses_uploads(): void
    {
        Sample::factory()->create();

        $this->ajax('examplePreviewGalleryPreview::onUpload', [
            'file_data' => UploadedFile::fake()->image('photo.png', 20, 20),
        ])->assertStatus(400)->assertJsonPath('__ajax.message', 'Access Denied');

        $this->assertSame(0, File::count());
    }
}
