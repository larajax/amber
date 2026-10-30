<?php

namespace Tests\Feature\Reference\Extending;

use App\Controllers\Reference\ExtendingController;
use App\Models\Sample;
use DateTimeZone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Facades\Ui;
use Larajax\Ui\Widgets\Form;
use Larajax\Ui\Widgets\Lists;
use Larajax\Ui\Widgets\Toolbar;
use Tests\TestCase;

class SeamsTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Ui::resolveUrlUsing(null);
        Ui::resolveLinkUsing('page', null);
        Ui::resolveLinkUsing('reference', null);
        Ui::resolveTimezoneUsing(null);

        parent::tearDown();
    }

    /**
     * ajax posts an AJAX handler request to the seams page, in the given viewer timezone.
     *
     * @param  array<string, mixed>  $data
     */
    protected function ajax(string $handler, array $data = [], ?string $timezone = null): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.extending.seams', array_filter(['timezone' => $timezone])), $data);
    }

    /**
     * makeList builds a samples list with a record URL, a linkage column on a stored link and one on a path.
     *
     * @param  array<string, mixed>  $config
     */
    protected function makeList(array $config = []): Lists
    {
        return Lists::createIn(new ExtendingController, $config + [
            'model' => new Sample,
            'recordUrl' => 'samples/:id/edit',
            'columns' => [
                'page_link' => ['label' => 'Link', 'type' => 'linkage'],
                'slug' => ['label' => 'Preview', 'type' => 'linkage', 'linkUrl' => 'samples/:id'],
            ],
        ]);
    }

    /**
     * renderColumn renders one column of the list for a sample.
     */
    protected function renderColumn(Lists $list, string $column, Sample $sample): string
    {
        return (string) $list->getColumnValue($sample, $list->getColumns()[$column]);
    }

    /**
     * makeSample builds an unsaved sample with id 7.
     *
     * @param  array<string, mixed>  $attributes
     */
    protected function makeSample(array $attributes = []): Sample
    {
        $sample = (new Sample)->forceFill($attributes);
        $sample->id = 7;

        return $sample;
    }

    /**
     * renderDatePicker renders a date picker on a sample published at 14:30 app time.
     *
     * @param  array<string, mixed>  $config
     */
    protected function renderDatePicker(array $config): string
    {
        $form = Form::createIn(new ExtendingController, [
            'model' => Sample::factory()->create(['published_at' => '2026-10-20 14:30:00']),
            'fields' => [
                'published_at' => $config + ['label' => 'Published', 'type' => 'datepicker'],
            ],
        ]);

        $form->bindToController();

        return (string) $form->render();
    }

    public function test_the_page_renders_every_example_beside_its_source(): void
    {
        Sample::factory()->create();

        $html = $this->get(route('reference.extending.seams'))->assertOk()->getContent();

        $sources = [
            'app/Controllers/Reference/ExtendingController/SeamResolvers.php',
            'resources/views/reference/extending/examples/seams-url.blade.php',
            'resources/ui/reference/extending/seams/path-buttons.yaml',
            'resources/ui/reference/extending/seams/path-columns.yaml',
            'resources/ui/reference/extending/seams/link-buttons.yaml',
            'resources/ui/reference/extending/seams/link-columns.yaml',
            'resources/views/reference/extending/examples/seams-timezone.blade.php',
            'resources/ui/reference/extending/seams/timezone-scopes.yaml',
            'resources/ui/reference/extending/seams/timezone-columns.yaml',
            'resources/ui/reference/extending/seams/page-link.yaml',
            'resources/ui/reference/extending/seams/published-at.yaml',
        ];

        foreach ($sources as $path) {
            $this->assertStringContainsString('<pre><code>'.e(trim(file_get_contents(base_path($path)))).'</code></pre>', $html, "{$path} is not shown.");
        }

        foreach (['resolvers', 'ui-url', 'widget-paths', 'scheme-links', 'timezone', 'timezone-lists', 'page-link', 'published-at'] as $section) {
            $this->assertStringContainsString('id="'.$section.'"', $html);
        }

        $this->assertStringContainsString('data-request="onSaveExamples"', $html);
    }

    public function test_widget_paths_on_the_page_go_through_the_url_resolver(): void
    {
        $sample = Sample::factory()->create(['slug' => 'brass-lamp']);

        $html = $this->get(route('reference.extending.seams'))->assertOk()->getContent();

        $pathUrl = fn (string $path) => e(route('reference.extending.seams', ['path' => $path]).'#widget-paths');

        $this->assertStringContainsString('href="'.$pathUrl("samples/{$sample->id}/edit").'"', $html);
        $this->assertStringContainsString('href="'.$pathUrl('samples/brass-lamp').'"', $html);
        $this->assertStringContainsString('href="'.$pathUrl('samples/create').'"', $html);
        $this->assertMatchesRegularExpression('/<code>\s*'.preg_quote($pathUrl('samples/create'), '/').'\s*<\/code>/', $html);
        $this->assertStringNotContainsString('The resolver received', $html);
    }

    public function test_the_page_shows_the_path_a_click_produced_escaped(): void
    {
        $this->get(route('reference.extending.seams', ['path' => 'samples/3/edit']))
            ->assertOk()
            ->assertSee('<h3>The resolver received samples/3/edit</h3>', false);

        $this->get(route('reference.extending.seams', ['path' => '<b>x</b>']))
            ->assertOk()
            ->assertSee('The resolver received &lt;b&gt;x&lt;/b&gt;', false)
            ->assertDontSee('<b>x</b>', false);
    }

    public function test_scheme_links_on_the_page_resolve_to_reference_pages(): void
    {
        Sample::factory()->create(['name' => 'Linked', 'page_link' => 'reference://ui/buttons']);
        Sample::factory()->create(['name' => 'Retired', 'page_link' => 'reference://extending/retired']);
        Sample::factory()->create(['name' => 'External', 'page_link' => 'https://larajax.org']);

        $html = $this->get(route('reference.extending.seams'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<a href="'.preg_quote(route('reference.ui.buttons'), '/').'"\s*>\/reference\/ui\/buttons<\/a>/', $html);
        $this->assertMatchesRegularExpression('/<a href="https:\/\/larajax.org"\s*>https:\/\/larajax.org<\/a>/', $html);
        $this->assertStringNotContainsString('href="reference://', $html);

        $this->assertMatchesRegularExpression('/<a\s+href="'.preg_quote(route('reference.ui.buttons'), '/').'"[^>]*>\s*<i class="ph ph-cursor-click" aria-hidden="true"><\/i>\s*Buttons page/', $html);
        $this->assertMatchesRegularExpression('/<button class="btn-solid theme-secondary" type="button">\s*<i class="ph ph-prohibit" aria-hidden="true"><\/i>\s*Retired page/', $html);

        $this->assertMatchesRegularExpression('/<code>\s*'.preg_quote(route('reference.ui.tabs'), '/').'\s*<\/code>/', $html);
        $this->assertMatchesRegularExpression('/<code>\s*An empty string\s*<\/code>/', $html);
    }

    public function test_the_page_shows_the_viewer_timezone(): void
    {
        $html = $this->get(route('reference.extending.seams', ['timezone' => 'Australia/Sydney']))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/class="nav-link active"\s+href="'.preg_quote(e(route('reference.extending.seams', ['timezone' => 'Australia/Sydney'])), '/').'#timezone"/', $html);
        $this->assertMatchesRegularExpression('/returns\s*<strong>\s*Australia\/Sydney\s*<\/strong>/', $html);

        $html = $this->get(route('reference.extending.seams', ['timezone' => 'Mars/Olympus']))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/returns\s*<strong>\s*UTC\s*<\/strong>/', $html);
    }

    public function test_the_page_resolvers_end_with_the_request(): void
    {
        $this->get(route('reference.extending.seams', ['timezone' => 'Australia/Sydney']))->assertOk();

        $this->assertSame(url('samples/create'), Ui::url('samples/create'));
        $this->assertSame('reference://ui/buttons', Ui::url('reference://ui/buttons'));
        $this->assertSame('UTC', Ui::timezone());
    }

    public function test_the_page_lists_datetimes_in_the_viewer_timezone_but_not_dates(): void
    {
        Sample::factory()->create(['published_at' => '2026-10-20 14:30:00', 'starts_on' => '2026-10-20']);

        $html = $this->get(route('reference.extending.seams', ['timezone' => 'Australia/Sydney']))->assertOk()->getContent();

        $this->assertStringContainsString('21 Oct 2026, 01:30 AEDT', $html);
        $this->assertStringContainsString('Oct 20, 2026', $html);
    }

    public function test_the_page_date_filter_reads_picked_days_in_the_viewer_timezone(): void
    {
        // 21 Oct 00:30 and 20 Oct 23:30 in Sydney
        Sample::factory()->create(['name' => 'Early sample', 'published_at' => '2026-10-20 13:30:00']);
        Sample::factory()->create(['name' => 'Late sample', 'published_at' => '2026-10-20 12:30:00']);

        $response = $this->ajax('timezoneFilter::onFilterUpdate', [
            'scopeName' => 'published_at',
            'Filter' => [
                'condition' => 'equals',
                'value' => '2026-10-21',
                'valueRaw' => '2026-10-21',
            ],
        ], 'Australia/Sydney');

        $response->assertOk();
        $response->assertSee('Early sample');
        $response->assertDontSee('Late sample');
    }

    public function test_the_page_date_picker_shows_the_value_in_the_viewer_timezone(): void
    {
        Sample::factory()->create(['published_at' => '2026-10-20 14:30:00']);

        $this->get(route('reference.extending.seams', ['timezone' => 'Australia/Sydney']))
            ->assertOk()
            ->assertSee('value="2026-10-21 01:30:00"', false)
            ->assertSee('data-timezone="Australia/Sydney"', false);
    }

    public function test_saving_the_page_stores_the_link_and_the_date_from_the_viewer_timezone(): void
    {
        $sample = Sample::factory()->create(['published_at' => '2026-10-20 14:30:00']);

        $this->ajax('onSaveExamples', [
            'page_link' => 'reference://ui/tabs',
            'published_at' => '2026-10-21 09:00:00',
        ], 'Australia/Sydney')->assertOk();

        $sample->refresh();
        $this->assertSame('reference://ui/tabs', $sample->page_link);
        $this->assertSame('2026-10-20 22:00:00', (string) $sample->getRawOriginal('published_at'));
    }

    public function test_without_a_resolver_widget_paths_use_the_app_url(): void
    {
        $list = $this->makeList();

        $this->assertSame(url('samples/7/edit'), $list->getRecordUrl($this->makeSample()));
        $this->assertStringContainsString('href="'.url('samples/7').'"', $this->renderColumn($list, 'slug', $this->makeSample(['slug' => 'lamp'])));
        $this->assertSame(url('samples/create'), Ui::url('samples/create'));
    }

    public function test_the_resolver_builds_record_linkage_and_toolbar_urls(): void
    {
        Ui::resolveUrlUsing(fn (string $path) => url('admin/'.ltrim($path, '/')));
        $list = $this->makeList();

        $this->assertSame(url('admin/samples/7/edit'), $list->getRecordUrl($this->makeSample()));
        $this->assertStringContainsString('href="'.url('admin/samples/7').'"', $this->renderColumn($list, 'slug', $this->makeSample(['slug' => 'lamp'])));

        $toolbar = Toolbar::createIn(new ExtendingController, [
            'buttons' => ['create' => ['label' => 'New Sample', 'url' => 'samples/create']],
        ]);
        $this->assertStringContainsString('href="'.url('admin/samples/create').'"', (string) $toolbar->render());
    }

    public function test_the_resolver_leaves_absolute_linkage_urls_alone(): void
    {
        Ui::resolveUrlUsing(fn (string $path) => url('admin/'.ltrim($path, '/')));
        $list = $this->makeList(['columns' => [
            'slug' => ['label' => 'Preview', 'type' => 'linkage', 'linkUrl' => 'https://example.com/:id'],
        ]]);

        $this->assertStringContainsString('href="https://example.com/7"', $this->renderColumn($list, 'slug', $this->makeSample(['slug' => 'lamp'])));
    }

    public function test_links_with_a_registered_scheme_resolve_to_their_address(): void
    {
        Ui::resolveLinkUsing('page', fn (string $link) => $link === 'page://about' ? url('about-us') : null);
        $list = $this->makeList();

        $html = $this->renderColumn($list, 'page_link', $this->makeSample(['page_link' => 'page://about']));
        $this->assertStringContainsString('href="'.url('about-us').'"', $html);
        $this->assertStringContainsString('>/about-us</a>', $html);

        $html = $this->renderColumn($list, 'page_link', $this->makeSample(['page_link' => 'page://missing']));
        $this->assertStringNotContainsString('<a', $html);
    }

    public function test_links_with_an_unregistered_scheme_stay_as_text(): void
    {
        $list = $this->makeList();

        $html = $this->renderColumn($list, 'page_link', $this->makeSample(['page_link' => 'page://about']));

        $this->assertSame('page://about', $html);
    }

    public function test_a_scheme_link_in_link_url_resolves_without_the_url_resolver(): void
    {
        Ui::resolveUrlUsing(fn (string $path) => url('admin/'.ltrim($path, '/')));
        Ui::resolveLinkUsing('page', fn (string $link) => url(substr($link, strlen('page://'))));

        $list = $this->makeList(['columns' => [
            'slug' => ['label' => 'Preview', 'type' => 'linkage', 'linkUrl' => 'page://products/:slug'],
        ]]);

        $html = $this->renderColumn($list, 'slug', $this->makeSample(['slug' => 'lamp']));
        $this->assertStringContainsString('href="'.url('products/lamp').'"', $html);
        $this->assertStringContainsString('>lamp</a>', $html);
    }

    public function test_toolbar_buttons_and_ui_url_resolve_scheme_links(): void
    {
        Ui::resolveUrlUsing(fn (string $path) => url('admin/'.ltrim($path, '/')));
        Ui::resolveLinkUsing('page', fn (string $link) => $link === 'page://about' ? url('about-us') : null);

        $this->assertSame(url('about-us'), Ui::url('page://about'));
        $this->assertSame('', Ui::url('page://missing'));
        $this->assertSame(url('admin/about'), Ui::url('about'));

        $toolbar = Toolbar::createIn(new ExtendingController, [
            'buttons' => [
                'about' => ['label' => 'About', 'url' => 'page://about'],
                'missing' => ['label' => 'Missing', 'url' => 'page://missing'],
            ],
        ]);

        $html = (string) $toolbar->render();
        $this->assertStringContainsString('href="'.url('about-us').'"', $html);
        $this->assertMatchesRegularExpression('/<button class="btn" type="button">\s*Missing\s*<\/button>/', $html);
    }

    public function test_without_a_resolver_the_user_timezone_is_the_app_timezone(): void
    {
        $this->assertSame('UTC', Ui::timezone());

        $html = $this->renderDatePicker([]);
        $this->assertStringContainsString('value="2026-10-20 14:30:00"', $html);
        $this->assertStringContainsString('data-timezone="UTC"', $html);
    }

    public function test_an_invalid_or_empty_timezone_falls_back_to_the_app_timezone(): void
    {
        Ui::resolveTimezoneUsing(fn () => 'Mars/Olympus');
        $this->assertSame('UTC', Ui::timezone());

        Ui::resolveTimezoneUsing(fn () => null);
        $this->assertSame('UTC', Ui::timezone());

        Ui::resolveTimezoneUsing(fn () => new DateTimeZone('Europe/Paris'));
        $this->assertSame('Europe/Paris', Ui::timezone());
    }

    public function test_the_datepicker_preview_uses_the_user_timezone(): void
    {
        Ui::resolveTimezoneUsing(fn () => 'Australia/Sydney');

        $form = Form::createIn(new ExtendingController, [
            'model' => Sample::factory()->create(['published_at' => '2026-10-20 14:30:00']),
            'previewMode' => true,
            'fields' => [
                'published_at' => ['label' => 'Published', 'type' => 'datepicker', 'format' => 'Y-m-d H:i T'],
            ],
        ]);

        $form->bindToController();

        $this->assertStringContainsString('2026-10-21 01:30 AEDT', (string) $form->render());
    }

    public function test_date_and_time_modes_and_use_timezone_false_do_not_convert(): void
    {
        Ui::resolveTimezoneUsing(fn () => 'Australia/Sydney');

        foreach ([['mode' => 'date'], ['mode' => 'time'], ['useTimezone' => false]] as $config) {
            $html = $this->renderDatePicker($config);

            $this->assertStringContainsString('value="2026-10-20 14:30:00"', $html);
            $this->assertStringContainsString('data-timezone="UTC"', $html);
            $this->assertStringContainsString('data-ignore-timezone', $html);
        }
    }

    public function test_list_columns_convert_datetime_values_but_not_dates_or_opted_out_columns(): void
    {
        Ui::resolveTimezoneUsing(fn () => 'Australia/Sydney');

        $list = Lists::createIn(new ExtendingController, [
            'model' => new Sample,
            'columns' => [
                'published_at' => ['label' => 'Published', 'type' => 'datetime', 'format' => 'Y-m-d H:i'],
                'starts_on' => ['label' => 'Starts On', 'type' => 'date', 'format' => 'Y-m-d'],
                'checked_at' => ['label' => 'Checked', 'type' => 'datetime', 'format' => '[Y-m-d H:i]', 'useTimezone' => false],
            ],
        ]);

        $sample = $this->makeSample([
            'published_at' => '2026-10-20 14:30:00',
            'starts_on' => '2026-10-20 14:30:00',
            'checked_at' => '2026-10-20 14:30:00',
        ]);

        $this->assertSame('2026-10-21 01:30', $this->renderColumn($list, 'published_at', $sample));
        $this->assertSame('2026-10-20', $this->renderColumn($list, 'starts_on', $sample));
        $this->assertSame('[2026-10-20 14:30]', $this->renderColumn($list, 'checked_at', $sample));
    }
}
