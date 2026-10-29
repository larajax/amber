<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Sample;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class KitchenSinkTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        // Rain removes the stored files with their records, which the rollback alone would leave behind
        File::all()->each->delete();

        parent::tearDown();
    }

    /**
     * ajax posts one AJAX handler request to a kitchen sink page.
     */
    protected function ajax(string $handler, string $url, array $data = []): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post($url, $data);
    }

    /**
     * makeSample creates a sample filled the way the seeder fills one.
     */
    protected function makeSample(array $attributes = []): Sample
    {
        return Sample::factory()->create($attributes + [
            'name' => 'Aurora Desk Lamp',
            'status' => 'active',
            'reference' => 'REF-1001',
            'country' => 'au',
            'state' => 'nsw',
            'step_style' => 'detailed',
            'meta' => ['seo' => ['title' => 'Aurora SEO title']],
            'address' => ['recipient' => 'Lumen Works', 'line1' => '12 Harbour Street'],
            'content' => [
                ['_group' => 'heading', 'title' => 'Light that follows you'],
                ['_group' => 'paragraph', 'text' => 'Holds its place.', 'align' => 'left'],
            ],
            'steps' => [['title' => 'Assemble the base', 'minutes' => 5, 'tasks' => []]],
            'headline' => 'The desk lamp that bends to your work',
        ]);
    }

    public function test_the_index_lists_the_samples_with_their_columns(): void
    {
        $owner = User::factory()->create(['name' => 'Olive Owner']);
        $this->makeSample(['user_id' => $owner->id, 'price' => 89.5]);

        $this->get('/kitchen-sink')
            ->assertOk()
            ->assertSee('Kitchen Sink')
            ->assertSee('New Sample')
            ->assertSee('Aurora Desk Lamp')
            ->assertSee('Olive Owner')
            ->assertSee('$ 89.50')
            ->assertSee('kitchen-sink/1/edit', false)
            ->assertSee('Active');
    }

    public function test_the_sidebar_links_the_kitchen_sink(): void
    {
        $this->get('/kitchen-sink')
            ->assertOk()
            ->assertSee('ph-flask', false)
            ->assertSee(url('kitchen-sink'), false);
    }

    public function test_the_create_page_renders_every_tab_with_the_create_context(): void
    {
        $html = $this->get('/kitchen-sink/create')->assertOk()->getContent();

        foreach (['Text', 'Choices', 'Dates &amp; Colors', 'Code', 'Relations', 'Files', 'Repeaters', 'Structured', 'Behaviour', 'Layout', 'Custom'] as $tab) {
            $this->assertMatchesRegularExpression('/<span class="title">\s*<span>\s*<span class="ph [^"]+"><\/span>\s*'.preg_quote($tab, '/').'\s*<\/span>/', $html);
        }

        // The create context shows its own hint and an editable reference
        $this->assertStringContainsString('This hint has context create', $html);
        $this->assertStringNotContainsString('This hint has context update', $html);
        $this->assertMatchesRegularExpression('/name="reference"[^>]*placeholder="REF-0001"/', $html);

        // Defaults apply to a new sample, and the session key reaches the page for deferred uploads
        $this->assertMatchesRegularExpression('/<input\s+type="hidden"\s+name="status"\s+value="draft"/', $html);
        $this->assertMatchesRegularExpression('/name="_session_key" value="[^"]{40}"/', $html);

        // The model's filterFields hides the postcode until a country is picked
        $this->assertDoesNotMatchRegularExpression('/<input[^>]*name="postcode"/', $html);
    }

    public function test_the_edit_page_renders_the_saved_values_and_the_update_context(): void
    {
        $owner = User::factory()->create(['name' => 'Olive Owner']);
        $sample = $this->makeSample(['user_id' => $owner->id]);

        $html = $this->get("/kitchen-sink/{$sample->id}/edit")->assertOk()->getContent();

        $this->assertStringContainsString('This hint has context update', $html);
        $this->assertMatchesRegularExpression('/name="reference"[^>]*value="REF-1001"[^>]*readonly/s', $html);

        // Presets, nested names, nested forms and the record finder
        $this->assertStringContainsString('data-input-preset="[name=&quot;name&quot;]"', $html);
        $this->assertStringContainsString('data-input-preset-type="camel"', $html);
        $this->assertMatchesRegularExpression('/name="meta\[seo\]\[title\]"[^>]*value="Aurora SEO title"/', $html);
        $this->assertMatchesRegularExpression('/name="address\[line1\]"[^>]*value="12 Harbour Street"/', $html);
        $this->assertStringContainsString('<span class="recordname">Olive Owner</span>', $html);

        // The builder repeater, its tabbed group and the trigger on the parent field
        $this->assertStringContainsString('data-control="repeaterbuilder"', $html);
        $this->assertMatchesRegularExpression('/name="content\[1\]\[text\]"/', $html);
        $this->assertStringContainsString('data-trigger="[name=&quot;step_style&quot;]"', $html);

        // The lazy tab waits for its first opening, and the secondary tabs open on Readme
        $this->assertStringContainsString('data-tab-name="Code"', $html);
        $this->assertStringNotContainsString('name="css_code"', $html);
        $this->assertMatchesRegularExpression('/<li class="active "\s*>\s*<a href="#secondarytab-readme"/', $html);

        // The postcode shows once a country is saved
        $this->assertMatchesRegularExpression('/<input[^>]*name="postcode"/', $html);
    }

    public function test_the_edit_page_loads_the_custom_widget_assets_in_the_head(): void
    {
        $sample = $this->makeSample();

        $html = $this->get("/kitchen-sink/{$sample->id}/edit")->assertOk()->getContent();
        $head = substr($html, 0, strpos($html, '</head>'));

        $this->assertStringContainsString('<link rel="stylesheet" href="'.asset('css/widgets/timechecker.css').'" />', $head);
        $this->assertStringContainsString('<script src="'.asset('js/widgets/timechecker.js').'" defer></script>', $head);
        $this->assertStringContainsString('data-control="timechecker"', $html);
    }

    public function test_on_store_saves_the_fields_relations_and_nested_values(): void
    {
        $owner = User::factory()->create();
        [$editors, $support] = UserGroup::factory()->count(2)->create();

        $resp = $this->ajax('onStore', '/kitchen-sink/create', [
            'name' => 'Ember Wall Clock',
            'slug' => 'ember-wall-clock',
            'reference' => 'REF-2001',
            'price' => '42.50',
            '_password' => 'never-stored',
            'status' => 'active',
            'is_featured' => '1',
            'features' => ['wifi', 'gym'],
            'user' => (string) $owner->id,
            'groups' => [(string) $editors->id, (string) $support->id],
            'tags' => ['clock', 'wall'],
            'keywords' => ['clock', 'brass'],
            'address' => ['recipient' => 'Ember Works', 'line1' => '3 Bell Lane'],
            'meta' => [
                'seo' => ['title' => 'Ember SEO title'],
                'options' => ['read_only' => 'Kept', 'disabled' => 'Dropped'],
            ],
        ]);

        $resp->assertOk();
        $sample = Sample::where('name', 'Ember Wall Clock')->firstOrFail();

        $this->assertSame(route('kitchen-sink.edit', $sample), $resp->json('__ajax.redirect'));
        $this->assertSame('REF-2001', $sample->reference);
        $this->assertEquals(42.5, (float) $sample->price);
        $this->assertTrue($sample->is_featured);
        $this->assertSame(['wifi', 'gym'], $sample->features);
        $this->assertSame($owner->id, $sample->user_id);
        $this->assertEqualsCanonicalizing([$editors->id, $support->id], $sample->groups()->pluck('user_groups.id')->all());
        $this->assertSame(['clock', 'wall'], $sample->tags);
        $this->assertSame('clock brass', $sample->keywords);
        $this->assertSame(['recipient' => 'Ember Works', 'line1' => '3 Bell Lane'], array_filter($sample->address));
        $this->assertSame('Ember SEO title', $sample->meta['seo']['title']);
        $this->assertSame('Kept', $sample->meta['options']['read_only']);
        $this->assertArrayNotHasKey('disabled', $sample->meta['options']);
        $this->assertArrayNotHasKey('_password', $sample->getAttributes());
    }

    public function test_on_store_requires_a_name(): void
    {
        $this->ajax('onStore', '/kitchen-sink/create', ['name' => ''])->assertStatus(422);

        $this->assertSame(0, Sample::count());
    }

    public function test_on_update_saves_changes_and_clears_the_groups(): void
    {
        $sample = $this->makeSample();
        $sample->groups()->attach(UserGroup::factory()->create());

        $this->ajax('onUpdate', "/kitchen-sink/{$sample->id}/edit", [
            'name' => 'Aurora Floor Lamp',
            'reference' => 'REF-9999',
            'groups' => '',
            'headline' => 'A taller lamp',
        ])->assertOk();

        $sample->refresh();
        $this->assertSame('Aurora Floor Lamp', $sample->name);
        $this->assertSame('A taller lamp', $sample->headline);
        $this->assertSame(0, $sample->groups()->count());

        // The update context makes the reference read-only, yet it still saves the posted value
        $this->assertSame('REF-9999', $sample->reference);
    }

    public function test_on_update_commits_uploads_held_against_the_session_key(): void
    {
        $sample = $this->makeSample();

        $file = new File;
        $file->data = UploadedFile::fake()->image('photo.png', 40, 40);
        $file->save();
        $sample->gallery()->add($file, 'session-1');

        $this->assertSame(0, $sample->gallery()->count());

        $this->ajax('onUpdate', "/kitchen-sink/{$sample->id}/edit", [
            'name' => $sample->name,
            '_session_key' => 'session-1',
        ])->assertOk();

        $this->assertSame(['photo.png'], $sample->fresh()->gallery->pluck('file_name')->all());
    }

    public function test_uploading_a_private_contract_stores_it_privately(): void
    {
        $sample = $this->makeSample();

        $resp = $this->ajax('formContract::onUpload', "/kitchen-sink/{$sample->id}/edit", [
            'file_data' => UploadedFile::fake()->create('contract.pdf', 1, 'application/pdf'),
            '_session_key' => 'session-1',
        ]);
        $this->assertSame(200, $resp->status(), $resp->getContent());

        $contract = $sample->contract()->withDeferred('session-1')->first();
        $this->assertSame('contract.pdf', $contract->file_name);
        $this->assertFalse((bool) $contract->is_public);
        $this->assertStringContainsString('/storage/uploads/protected/', $resp->json('path'));
    }

    public function test_the_brochure_refuses_files_that_are_not_pdf(): void
    {
        $sample = $this->makeSample();

        $this->ajax('formBrochure::onUpload', "/kitchen-sink/{$sample->id}/edit", [
            'file_data' => UploadedFile::fake()->create('brochure.txt', 1, 'text/plain'),
        ])->assertStatus(400);

        $this->assertSame(0, File::count());
    }

    public function test_on_destroy_deletes_the_sample(): void
    {
        $sample = $this->makeSample();

        $resp = $this->ajax('onDestroy', "/kitchen-sink/{$sample->id}/edit");

        $resp->assertOk();
        $this->assertSame(route('kitchen-sink.index'), $resp->json('__ajax.redirect'));
        $this->assertNull(Sample::find($sample->id));
    }

    public function test_the_headline_change_handler_refreshes_the_stats_partial(): void
    {
        $sample = $this->makeSample();

        $resp = $this->ajax('onChangeHeadline', "/kitchen-sink/{$sample->id}/edit", [
            'name' => $sample->name,
            'headline' => 'A very long headline that goes on and on well past the sixty character limit',
        ]);

        $resp->assertOk();
        $op = $resp->json('__ajax.ops.0');
        $this->assertStringEndsWith('-field-_headline_stats-group', $op['selector']);
        $this->assertStringContainsString('76 characters', $op['html']);
        $this->assertStringContainsString('Too long', $op['html']);

        // The handler only previews the value, it does not save it
        $this->assertSame('The desk lamp that bends to your work', $sample->fresh()->headline);
    }

    public function test_the_timezone_handler_searches_the_timezones(): void
    {
        $resp = $this->ajax('onSearchTimezones', '/kitchen-sink/create', ['term' => 'sydney']);

        $resp->assertOk();
        $this->assertSame([['id' => 'Australia/Sydney', 'text' => 'Australia/Sydney']], $resp->json('results'));
    }

    public function test_refreshing_the_state_reloads_its_options_for_the_posted_country(): void
    {
        $sample = $this->makeSample();

        $resp = $this->ajax('form::onRefresh', "/kitchen-sink/{$sample->id}/edit", [
            'country' => 'us',
            'fields' => ['state', 'postcode'],
        ]);

        $resp->assertOk();
        $html = collect($resp->json('__ajax.ops'))->pluck('html')->implode('');
        $this->assertStringContainsString('California', $html);
        $this->assertStringNotContainsString('New South Wales', $html);
        $this->assertStringContainsString('ZIP Code', $html);
    }

    public function test_opening_the_lazy_code_tab_renders_its_editors(): void
    {
        $sample = $this->makeSample(['css_code' => '.lamp { color: red; }']);

        $resp = $this->ajax('form::onLazyLoadTab', "/kitchen-sink/{$sample->id}/edit", [
            'target' => '#code-pane',
            'name' => 'Code',
        ]);

        $resp->assertOk();
        $html = $resp->json('__ajax.ops.0.html');
        $this->assertStringContainsString('data-control="codeeditor"', $html);
        $this->assertStringContainsString('.lamp { color: red; }', $html);
        $this->assertStringContainsString('name="html_code"', $html);
    }

    public function test_the_owner_finder_lists_only_verified_users(): void
    {
        $sample = $this->makeSample();
        User::factory()->create(['name' => 'Vera Verified']);
        User::factory()->unverified()->create(['name' => 'Una Unverified']);

        $resp = $this->ajax('formUser::onFindRecord', "/kitchen-sink/{$sample->id}/edit", ['recordfinder_flag' => 1]);

        $resp->assertOk();
        $this->assertStringContainsString('Pick an owner', $resp->getContent());
        $this->assertStringContainsString('Vera Verified', $resp->getContent());
        $this->assertStringNotContainsString('Una Unverified', $resp->getContent());
    }

    public function test_the_owner_finder_searches_the_start_of_a_name_or_email(): void
    {
        $sample = $this->makeSample();
        User::factory()->create(['name' => 'Gemma Stone', 'email' => 'gemma@example.com']);
        User::factory()->create(['name' => 'Angela Moss', 'email' => 'angela@example.com']);

        $resp = $this->ajax('formUserSearch::onSubmit', "/kitchen-sink/{$sample->id}/edit", [
            'recordfinder_flag' => 1,
            'formUserSearch' => ['term' => 'ge'],
        ]);

        $resp->assertOk();
        $html = collect($resp->json('__ajax.ops'))->pluck('html')->implode('');
        $this->assertStringContainsString('Gemma Stone', $html);
        $this->assertStringNotContainsString('Angela Moss', $html);
    }
}
