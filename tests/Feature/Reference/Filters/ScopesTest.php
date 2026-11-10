<?php

namespace Tests\Feature\Reference\Filters;

use App\Controllers\Reference\FiltersController;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Sample;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Widgets\Filter;
use October\Rain\Exception\SystemException;
use Tests\TestCase;

class ScopesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ajax posts an AJAX request for the given handler to the Scope Types page.
     *
     * @param  array<string, mixed>  $data
     */
    protected function ajax(string $handler, array $data = []): TestResponse
    {
        // Laravel keeps the route's controller across a test's requests, and the controller keeps its first AJAX handler
        app('router')->getRoutes()->getByName('reference.filters.scopes')->flushController();

        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.filters.scopes'), $data);
    }

    /**
     * listPatch returns the refreshed markup of the list an AJAX response updates.
     */
    protected function listPatch(TestResponse $response, string $alias): string
    {
        $patch = collect($response->assertOk()->json('__ajax.ops'))->firstWhere('selector', '#Lists-'.$alias);

        $this->assertNotNull($patch, "The response does not refresh the {$alias} list.");

        return $patch['html'];
    }

    /**
     * renderScopeForm renders the popover form of a scope with the given configuration.
     *
     * @param  array<string, mixed>  $config
     */
    protected function renderScopeForm(array $config): string
    {
        $filter = Filter::createIn(new FiltersController, [
            'model' => new Post,
            'scopes' => ['scoped' => $config],
        ]);

        $filter->bindToController();

        return (string) $filter->renderScopeFormElement('scoped');
    }

    /**
     * filteredValues applies a scope with the given configuration and value to a query of the model, returning one column of the matching records.
     *
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $value
     * @return array<int, mixed>
     */
    protected function filteredValues(Model $model, array $config, array $value, string $column): array
    {
        $filter = Filter::createIn(new FiltersController, [
            'model' => $model,
            'scopes' => ['scoped' => $config],
        ]);

        $filter->bindToController();
        $filter->putScopeValue('scoped', $value);

        return $filter->applyAllScopesToQuery($model->newQuery())->orderBy('id')->pluck($column)->all();
    }

    public function test_the_page_renders_every_example_beside_its_yaml(): void
    {
        $html = $this->get(route('reference.filters.scopes'))->assertOk()->getContent();

        foreach (['checkbox', 'switch', 'dropdown', 'group', 'date', 'text', 'number'] as $example) {
            $this->assertStringContainsString("resources/ui/reference/filters/scopes/{$example}.yaml", $html);
            $this->assertStringContainsString('id="Filter-'.$example.'Filter"', $html);
            $this->assertStringContainsString('id="Lists-'.$example.'List"', $html);
        }

        $this->assertStringContainsString('resources/ui/reference/filters/scopes/trashed.yaml', $html);
        $this->assertStringContainsString('resources/ui/reference/filters/comments.yaml', $html);
        $this->assertStringContainsString('id="Filter-trashedFilter"', $html);
        $this->assertStringContainsString('id="Lists-trashedList"', $html);
        $this->assertStringContainsString('function makeTrashedExample(', $html);

        $this->assertStringContainsString('function makeScopeExamples(', $html);
        $this->assertStringContainsString('resources/ui/reference/filters/posts.yaml', $html);
        $this->assertStringContainsString('data-request="checkboxFilter::onFilterClearAll"', $html);
    }

    public function test_each_scope_type_renders_its_control(): void
    {
        $html = $this->get(route('reference.filters.scopes'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/aria-pressed="false"\s+class="filter-scope scope-checkbox\s*"\s+data-scope-name="published"/', $html);
        $this->assertMatchesRegularExpression('/class="filter-scope filter-chip scope-switch\s*"\s+data-scope-name="published">\s*<button\s+type="button"\s+id="Filter-switchFilter-scope-published"\s+class="combobox-toggle"/', $html);
        $this->assertMatchesRegularExpression('/aria-label="Published: Yes"\s+data-bs-value="2"\s*>\s*<i class="ph ph-check filter-mark" aria-hidden="true"><\/i>/', $html);
        $this->assertMatchesRegularExpression('/aria-label="Published: No"\s+data-bs-value="1"\s*>\s*<i class="ph ph-x filter-mark" aria-hidden="true"><\/i>/', $html);
        $this->assertMatchesRegularExpression('/data-bs-value="draft"\s*>\s*<span class="status-indicator" style="background: var\(--bs-orange-500\)" aria-hidden="true"><\/span>/', $html);
        $this->assertMatchesRegularExpression('/data-bs-value=""\s*>\s*<span class="menu-item-content">\s*<span>\s*Any status\s*<\/span>/', $html);

        foreach (['category', 'published_at', 'review_on', 'title', 'views', 'id'] as $scopeName) {
            $this->assertMatchesRegularExpression('/<a\s+href="javascript:;"\s+class="filter-scope\s*"\s+data-scope-name="'.$scopeName.'"/', $html);
        }
    }

    public function test_the_checkbox_scope_matches_its_column(): void
    {
        Post::factory()->published()->create(['title' => 'Live Article']);
        Post::factory()->create(['title' => 'Hidden Article']);

        $html = $this->listPatch($this->ajax('checkboxFilter::onFilterUpdate', ['scopeName' => 'published', 'value' => '1']), 'checkboxList');

        $this->assertStringContainsString('Live Article', $html);
        $this->assertStringNotContainsString('Hidden Article', $html);
    }

    public function test_the_checkbox_scope_runs_its_sql_condition(): void
    {
        $discussed = Post::factory()->create(['title' => 'Discussed Article']);
        Comment::factory()->for($discussed)->create();
        Post::factory()->create(['title' => 'Silent Article']);

        $html = $this->listPatch($this->ajax('checkboxFilter::onFilterUpdate', ['scopeName' => 'commented', 'value' => '1']), 'checkboxList');

        $this->assertStringContainsString('Discussed Article', $html);
        $this->assertStringNotContainsString('Silent Article', $html);
    }

    public function test_the_switch_scope_applies_its_second_condition(): void
    {
        Post::factory()->published()->create(['title' => 'Live Article']);
        Post::factory()->create(['title' => 'Hidden Article']);

        $html = $this->listPatch($this->ajax('switchFilter::onFilterUpdate', ['scopeName' => 'published', 'value' => '2']), 'switchList');

        $this->assertStringContainsString('Live Article', $html);
        $this->assertStringNotContainsString('Hidden Article', $html);
    }

    public function test_an_active_switch_shows_a_check_or_cross_with_words_for_assistive_tech_only(): void
    {
        foreach ([2 => ['ph ph-check filter-mark', 'Yes'], 1 => ['ph ph-x filter-mark', 'No']] as $value => [$icon, $words]) {
            $html = $this->withSession([
                'widget.app_controllers-FiltersController-Filter-switchFilter' => json_encode(['scope-published' => ['value' => $value]]),
            ])->get(route('reference.filters.scopes'))->assertOk()->getContent();

            $this->assertMatchesRegularExpression('/class="filter-scope filter-chip scope-switch active"\s+data-scope-name="published">\s*<button\s+type="button"\s+class="filter-chip-clear"/', $html);
            $this->assertStringContainsString('<span aria-hidden="true" data-scope-mark><i class="'.$icon.'" aria-hidden="true"></i></span>', $html);
            $this->assertStringContainsString('<span class="combobox-value visually-hidden">'.$words.'</span>', $html);
        }
    }

    public function test_the_switch_scope_applies_its_first_condition(): void
    {
        Post::factory()->published()->create(['title' => 'Live Article']);
        Post::factory()->create(['title' => 'Hidden Article']);

        $html = $this->listPatch($this->ajax('switchFilter::onFilterUpdate', ['scopeName' => 'published', 'value' => '1']), 'switchList');

        $this->assertStringContainsString('Hidden Article', $html);
        $this->assertStringNotContainsString('Live Article', $html);
    }

    public function test_the_dropdown_scope_matches_the_picked_value(): void
    {
        Post::factory()->create(['title' => 'Draft Article', 'status' => 'draft']);
        Post::factory()->create(['title' => 'Approved Article', 'status' => 'approved']);

        $html = $this->listPatch($this->ajax('dropdownFilter::onFilterUpdate', ['scopeName' => 'status', 'value' => 'approved']), 'dropdownList');

        $this->assertStringContainsString('Approved Article', $html);
        $this->assertStringNotContainsString('Draft Article', $html);
    }

    public function test_the_dropdown_scope_shows_the_picked_option_with_its_status_dot_beside_the_label(): void
    {
        $html = $this->withSession([
            'widget.app_controllers-FiltersController-Filter-dropdownFilter' => json_encode(['scope-status' => ['value' => 'draft']]),
        ])->get(route('reference.filters.scopes'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/class="filter-scope filter-chip scope-dropdown active"\s+data-scope-name="status">\s*<button\s+type="button"\s+class="filter-chip-clear"/', $html);
        $this->assertStringContainsString('<span aria-hidden="true" data-scope-mark><span class="status-indicator" style="background: var(--bs-orange-500)" aria-hidden="true"></span></span>', $html);
        $this->assertMatchesRegularExpression('/<span class="combobox-value filter-value">\s*Draft\s*<\/span>/', $html);
        $this->assertMatchesRegularExpression('/class="menu-item selected"\s+role="option"\s+aria-selected="true"\s+data-bs-value="draft"/', $html);
    }

    public function test_the_funnel_counts_the_active_scopes_and_the_scopes_keep_their_order(): void
    {
        Category::factory()->count(2)->create();
        $categoryId = Category::query()->value('id');

        $html = $this->withSession([
            'widget.app_controllers-FiltersController-Filter-dropdownFilter' => json_encode([
                'scope-category' => ['value' => $categoryId],
                'scope-status' => ['value' => 'draft'],
            ]),
        ])->get(route('reference.filters.scopes'))->assertOk()->getContent();

        // The badge sits in the funnel just before the filter's setup menu
        $this->assertMatchesRegularExpression('/<span class="filter-count" data-filter-count\s*>\s*<span data-filter-count-value>2<\/span>\s*<span class="visually-hidden">active<\/span>\s*<\/span>\s*<\/a>\s*<div class="menu control-dropdown" role="menu" id="Filter-dropdownFilter-setupMenu">/', $html);

        // Status comes before category in the YAML, active or not
        $this->assertLessThan(strpos($html, 'data-scope-name="category"'), strpos($html, 'data-scope-name="status"'));

        // A filter with nothing active hides its count
        $this->assertMatchesRegularExpression('/<span class="filter-count" data-filter-count hidden>\s*<span data-filter-count-value>0<\/span>.*?id="Filter-checkboxFilter-setupMenu"/s', $html);
    }

    public function test_a_dropdown_scope_shows_an_option_icon_beside_the_label(): void
    {
        $filter = Filter::createIn(new FiltersController, [
            'model' => new Post,
            'scopes' => ['transport' => [
                'label' => 'Transport',
                'type' => 'dropdown',
                'default' => 'air',
                'options' => ['road' => ['Road', 'ph ph-truck'], 'air' => ['Air', 'ph ph-airplane-tilt']],
            ]],
        ]);

        $filter->bindToController();
        $html = (string) $filter->renderScopeElement('transport');

        $this->assertStringContainsString('<span aria-hidden="true" data-scope-mark><i class="ph ph-airplane-tilt" aria-hidden="true"></i></span>', $html);
        $this->assertMatchesRegularExpression('/data-bs-value="road"\s*>\s*<i class="ph ph-truck" aria-hidden="true"><\/i>/', $html);
    }

    public function test_a_long_dropdown_offers_a_search_box_and_a_short_one_does_not(): void
    {
        // Twelve categories and the empty option make a list longer than ten, one named like a language file
        Category::factory()->count(11)->create();
        Category::factory()->create(['name' => 'Validation']);

        $html = $this->get(route('reference.filters.scopes'))->assertOk()->getContent();

        $category = $this->scopeHtml($html, 'category');
        $this->assertMatchesRegularExpression('/data-bs-search-normalize="true"/', $category);
        // The same search field as the group scope's, above the options, which scroll on their own
        $this->assertMatchesRegularExpression('/<div data-control="search-input" class="filter-search combobox-search">\s*<label class="form-control form-adorn form-control-sm">\s*<span class="form-adorn-icon" aria-hidden="true">\s*<i class="ph ph-magnifying-glass" aria-hidden="true"><\/i>\s*<\/span>\s*<input[^>]*placeholder="Search..."[^>]*class="form-ghost combobox-search-input"[^>]*\/>\s*<button\s+class="clear-input-text"[^>]*data-search-clear\s*>/', $category);
        $this->assertMatchesRegularExpression('/<\/label>\s*<\/div>\s*<div class="combobox-options">\s*<button/', $category);
        $this->assertStringNotContainsString('data-request', $category);
        $this->assertMatchesRegularExpression('/<div class="combobox-no-results d-none">\s*No results/', $category);
        $this->assertSame(13, substr_count($category, 'role="option"'));
        $this->assertMatchesRegularExpression('/<span>\s*Validation\s*<\/span>/', $category);

        $status = $this->scopeHtml($html, 'status');
        $this->assertStringNotContainsString('combobox-search', $status);
        $this->assertStringNotContainsString('data-bs-search-normalize', $status);
    }

    public function test_the_search_setting_shows_or_hides_the_box_whatever_the_length(): void
    {
        $options = ['a' => 'A', 'b' => 'B'];
        $many = array_combine(range(1, 20), range(1, 20));

        $this->assertStringContainsString('combobox-search-input', $this->renderDropdown(['options' => $options, 'search' => true]));
        $this->assertStringNotContainsString('combobox-search-input', $this->renderDropdown(['options' => $many, 'search' => false]));
        $this->assertStringContainsString('combobox-search-input', $this->renderDropdown(['options' => $many]));
        $this->assertStringNotContainsString('combobox-search-input', $this->renderDropdown(['options' => array_slice($many, 0, 10, true)]));
    }

    /**
     * scopeHtml returns the markup of one scope on the Scope Types page.
     */
    protected function scopeHtml(string $html, string $scopeName): string
    {
        $this->assertMatchesRegularExpression('/data-scope-name="'.$scopeName.'">(.*?)<\/div>\s*<\/div>/s', $html);
        preg_match('/data-scope-name="'.$scopeName.'">(.*?)<\/div>\s*<\/div>/s', $html, $match);

        return $match[1];
    }

    /**
     * renderDropdown renders a dropdown scope with the given settings in a filter of posts.
     *
     * @param  array<string, mixed>  $config
     */
    protected function renderDropdown(array $config): string
    {
        $filter = Filter::createIn(new FiltersController, [
            'model' => new Post,
            'scopes' => ['pick' => ['label' => 'Pick', 'type' => 'dropdown'] + $config],
        ]);

        $filter->bindToController();

        return (string) $filter->renderScopeElement('pick');
    }

    public function test_the_dropdown_scope_shows_its_empty_option_without_a_mark(): void
    {
        $html = $this->get(route('reference.filters.scopes'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/class="filter-scope filter-chip scope-dropdown\s*"\s+data-scope-name="status">\s*<button\s+type="button"\s+id="Filter-dropdownFilter-scope-status"/', $html);
        $this->assertStringContainsString('<span aria-hidden="true" data-scope-mark></span>', $html);
        $this->assertMatchesRegularExpression('/<span class="combobox-value filter-value">\s*Any status\s*<\/span>/', $html);
        $this->assertMatchesRegularExpression('/class="menu-item selected"\s+role="option"\s+aria-selected="true"\s+data-bs-value=""/', $html);
    }

    public function test_the_category_group_scope_filters_by_the_picked_categories(): void
    {
        $guides = Category::factory()->create(['name' => 'Guides', 'slug' => 'guides']);
        Post::factory()->for($guides)->create(['title' => 'Guide Article']);
        Post::factory()->create(['title' => 'Other Article']);

        $response = $this->ajax('groupFilter::onFilterUpdate', [
            'scopeName' => 'category',
            'Filter' => ['value' => json_encode([$guides->id])],
        ]);

        $html = $this->listPatch($response, 'groupList');
        $this->assertStringContainsString('Guide Article', $html);
        $this->assertStringNotContainsString('Other Article', $html);

        $scope = collect($response->json('__ajax.ops'))->firstWhere('selector', '#Filter-groupFilter-scope-category-group');
        $this->assertStringContainsString('<div class="filter-chip active">', $scope['html']);
        $this->assertStringContainsString('<span class="filter-value">1</span>', $scope['html']);
    }

    public function test_the_status_group_scope_matches_the_column_against_its_options(): void
    {
        Post::factory()->create(['title' => 'Draft Article', 'status' => 'draft']);
        Post::factory()->create(['title' => 'Review Article', 'status' => 'review']);
        Post::factory()->create(['title' => 'Approved Article', 'status' => 'approved']);

        $html = $this->listPatch($this->ajax('groupFilter::onFilterUpdate', [
            'scopeName' => 'status',
            'Filter' => ['value' => json_encode(['draft', 'review'])],
        ]), 'groupList');

        $this->assertStringContainsString('Draft Article', $html);
        $this->assertStringContainsString('Review Article', $html);
        $this->assertStringNotContainsString('Approved Article', $html);
    }

    public function test_the_category_group_options_come_from_the_relation(): void
    {
        Category::factory()->create(['name' => 'Guides', 'slug' => 'guides']);
        Category::factory()->create(['name' => 'News', 'slug' => 'news']);

        $names = collect($this->ajax('groupFilterCategory::onGetGroupOptions')->assertOk()->json('options.available'))
            ->pluck('name')
            ->all();

        $this->assertEqualsCanonicalizing(['Guides', 'News'], $names);
    }

    public function test_group_options_named_like_a_language_file_keep_their_name(): void
    {
        $category = Category::factory()->create(['name' => 'validation', 'slug' => 'validation']);

        $available = $this->ajax('groupFilterCategory::onGetGroupOptions')->assertOk()->json('options.available');

        $this->assertSame([['id' => $category->id, 'name' => 'validation']], $available);
    }

    public function test_the_group_search_is_the_search_field_with_an_icon_and_a_clear_button(): void
    {
        $html = $this->ajax('groupFilter::onLoadFilterForm', ['scopeName' => 'status'])->assertOk()->json('result');

        $this->assertMatchesRegularExpression('/<div data-control="search-input" class="filter-search">\s*<label class="form-control form-adorn form-control-sm">\s*<span class="form-adorn-icon" aria-hidden="true">\s*<i class="ph ph-magnifying-glass" aria-hidden="true"><\/i>\s*<\/span>\s*<input[^>]*name="search"[^>]*aria-label="Search..."[^>]*class="form-ghost filter-search-input popup-allow-focus"[^>]*data-request="groupFilterStatus::onGetGroupOptions"[^>]*\/>\s*<button\s+class="clear-input-text"[^>]*data-search-clear\s*>/', $html);
        $this->assertStringNotContainsString('search-input-container', $html);
    }

    public function test_the_group_popover_is_named_for_assistive_tech_without_a_visible_title(): void
    {
        $html = $this->ajax('groupFilter::onLoadFilterForm', ['scopeName' => 'status'])->assertOk()->json('result');

        $this->assertMatchesRegularExpression('/data-control="groupfilter"[^>]*role="group"\s+aria-label="Status"/s', $html);
        $this->assertStringNotContainsString('filter-title', $html);
        $this->assertStringNotContainsString('<h3', $html);
    }

    public function test_the_date_filter_form_offers_relative_periods_first_and_posts_the_raw_date_inputs(): void
    {
        $content = $this->ajax('dateFilter::onLoadFilterForm', ['scopeName' => 'published_at'])->assertOk()->json('result');

        preg_match('/<select name="Filter\[condition\]".*?<\/select>/s', $content, $select);
        preg_match_all('/<option\s+value="(\w+)"\s*>\s*([^<]+)</', $select[0], $options);
        $this->assertSame(['inLast', 'inNext', 'equals', 'between', 'after', 'before', 'empty', 'notEmpty'], $options[1]);
        $this->assertSame(['is in the last', 'is in the next', 'is on', 'is between', 'is on or after', 'is before or on', 'is empty', 'is not empty'], array_map('trim', $options[2]));

        // A relative period starts as the last 7 days, and each row shows for its own conditions
        $this->assertMatchesRegularExpression('/data-trigger-action="show"\s+data-trigger-condition="value\[inLast\]\[inNext\]"/', $content);
        $this->assertMatchesRegularExpression('/name="Filter\[count\]"\s+value="7"/', $content);
        $this->assertStringContainsString('<option value="days" selected="selected">days</option>', $content);
        $this->assertMatchesRegularExpression('/data-trigger-action="hide"\s+data-trigger-condition="value\[between\]\[inLast\]\[inNext\]\[empty\]\[notEmpty\]"/', $content);

        // The date widget only applies when the raw inputs are posted
        $this->assertStringContainsString('Filter[valueRaw]', $content);
        $this->assertStringContainsString('Filter[afterRaw]', $content);
        $this->assertStringContainsString('Filter[beforeRaw]', $content);
    }

    public function test_date_filter_inputs_sit_in_adorned_fields_with_a_calendar_icon(): void
    {
        $html = $this->renderScopeForm(['label' => 'Published', 'type' => 'date']);

        $this->assertSame(3, preg_match_all('/<label class="form-control form-control-sm form-adorn form-adorn-end w-120">\s*<span class="form-adorn-icon" aria-hidden="true">\s*<i class="ph ph-calendar-blank" aria-hidden="true"><\/i>\s*<\/span>\s*<input\s+type="text"\s+name="Filter\[(?:valueRaw|afterRaw|beforeRaw)\]"[^>]*class="form-ghost popup-allow-focus"[^>]*data-datepicker \/>\s*<\/label>/', $html));
    }

    public function test_the_date_filter_applies_an_after_condition(): void
    {
        Post::factory()->create(['title' => 'Old Article', 'published' => true, 'published_at' => '2020-01-01 10:00:00']);
        Post::factory()->create(['title' => 'New Article', 'published' => true, 'published_at' => '2025-06-01 10:00:00']);

        $html = $this->listPatch($this->ajax('dateFilter::onFilterUpdate', [
            'scopeName' => 'published_at',
            'Filter' => [
                'condition' => 'after',
                'value' => '2024-01-01',
                'valueRaw' => '2024-01-01',
            ],
        ]), 'dateList');

        $this->assertStringContainsString('New Article', $html);
        $this->assertStringNotContainsString('Old Article', $html);
    }

    public function test_the_daterange_scope_offers_only_between(): void
    {
        $content = $this->ajax('dateFilter::onLoadFilterForm', ['scopeName' => 'review_on'])->assertOk()->json('result');

        $this->assertStringContainsString('<input type="hidden" name="Filter[condition]" value="between" />', $content);
        $this->assertStringNotContainsString('Filter[valueRaw]', $content);
    }

    public function test_the_daterange_scope_matches_dates_between_two_days(): void
    {
        Post::factory()->create(['title' => 'January Review', 'review_on' => '2026-01-15']);
        Post::factory()->create(['title' => 'March Review', 'review_on' => '2026-03-15']);

        $html = $this->listPatch($this->ajax('dateFilter::onFilterUpdate', [
            'scopeName' => 'review_on',
            'Filter' => [
                'condition' => 'between',
                'after' => '2026-01-01',
                'afterRaw' => '2026-01-01',
                'before' => '2026-01-31',
                'beforeRaw' => '2026-01-31',
            ],
        ]), 'dateList');

        $this->assertStringContainsString('January Review', $html);
        $this->assertStringNotContainsString('March Review', $html);
    }

    public function test_the_date_filter_matches_the_last_days_including_today_and_labels_its_chip(): void
    {
        $this->travelTo('2026-11-02 12:00:00');

        Post::factory()->create(['title' => 'Today Article', 'published_at' => '2026-11-02 08:00:00']);
        Post::factory()->create(['title' => 'Sixth Day Article', 'published_at' => '2026-10-27 09:00:00']);
        Post::factory()->create(['title' => 'Week Old Article', 'published_at' => '2026-10-26 23:00:00']);

        $response = $this->ajax('dateFilter::onFilterUpdate', [
            'scopeName' => 'published_at',
            'Filter' => ['condition' => 'inLast', 'count' => '7', 'unit' => 'days', 'value' => '', 'valueRaw' => ''],
        ]);

        $html = $this->listPatch($response, 'dateList');
        $this->assertStringContainsString('Today Article', $html);
        $this->assertStringContainsString('Sixth Day Article', $html);
        $this->assertStringNotContainsString('Week Old Article', $html);

        $chip = collect($response->json('__ajax.ops'))->firstWhere('selector', '#Filter-dateFilter-scope-published_at-group')['html'];
        $this->assertStringContainsString('<span class="filter-value">Last 7 days</span>', $chip);

        // The scope keeps the period, not its dates, so a saved filter moves with the days
        $stored = json_decode(session('widget.app_controllers-FiltersController-Filter-dateFilter'), true);
        $this->assertSame(['condition' => 'inLast', 'count' => 7, 'unit' => 'days'], $stored['scope-published_at']);
    }

    public function test_relative_dates_count_whole_days_back_or_forward_from_today(): void
    {
        $this->travelTo('2026-11-02 12:00:00');

        foreach (['2026-10-02 23:00:00', '2026-10-03 00:30:00', '2026-11-01 10:00:00', '2026-11-15 22:00:00', '2026-11-16 01:00:00'] as $date) {
            Post::factory()->create(['title' => $date, 'published_at' => $date]);
        }

        $titles = fn (array $value) => $this->filteredValues(new Post, ['type' => 'date', 'valueFrom' => 'published_at'], $value, 'title');

        $this->assertSame(['2026-10-03 00:30:00', '2026-11-01 10:00:00'], $titles(['condition' => 'inLast', 'count' => 1, 'unit' => 'months']));
        $this->assertSame(['2026-11-15 22:00:00'], $titles(['condition' => 'inNext', 'count' => 2, 'unit' => 'weeks']));
        $this->assertSame([], $titles(['condition' => 'inLast', 'count' => 1, 'unit' => 'days']));

        // The same stored period covers different days once time passes
        $this->travelTo('2026-11-16 09:00:00');
        $this->assertSame(['2026-11-15 22:00:00', '2026-11-16 01:00:00'], $titles(['condition' => 'inLast', 'count' => 2, 'unit' => 'days']));
    }

    public function test_a_relative_date_without_a_count_or_a_known_unit_applies_no_filter(): void
    {
        $this->ajax('dateFilter::onFilterUpdate', [
            'scopeName' => 'published_at',
            'Filter' => ['condition' => 'inLast', 'count' => '0', 'unit' => 'days'],
        ])->assertOk();
        $this->assertNull(json_decode(session('widget.app_controllers-FiltersController-Filter-dateFilter'), true)['scope-published_at']);

        $this->ajax('dateFilter::onFilterUpdate', [
            'scopeName' => 'published_at',
            'Filter' => ['condition' => 'inLast', 'count' => '3', 'unit' => 'decades'],
        ])->assertOk();
        $this->assertNull(json_decode(session('widget.app_controllers-FiltersController-Filter-dateFilter'), true)['scope-published_at']);
    }

    public function test_each_picked_date_counts_as_its_whole_day(): void
    {
        Post::factory()->create(['title' => 'Last Day Article', 'published_at' => '2026-10-30 22:00:00']);
        Post::factory()->create(['title' => 'Next Day Article', 'published_at' => '2026-10-31 00:30:00']);

        $titles = fn (array $value) => $this->filteredValues(new Post, ['type' => 'date', 'valueFrom' => 'published_at', 'useTimezone' => false], $value, 'title');

        $this->assertSame(['Last Day Article'], $titles(['condition' => 'between', 'after' => '2026-10-01', 'before' => '2026-10-30']));
        $this->assertSame(['Last Day Article'], $titles(['condition' => 'before', 'value' => '2026-10-30']));
        $this->assertSame(['Last Day Article'], $titles(['condition' => 'equals', 'value' => '2026-10-30']));
        $this->assertSame(['Last Day Article', 'Next Day Article'], $titles(['condition' => 'after', 'value' => '2026-10-30']));
    }

    public function test_a_named_not_equals_condition_is_still_offered_and_applied(): void
    {
        $form = $this->renderScopeForm(['label' => 'Published', 'type' => 'date', 'conditions' => ['equals' => true, 'notEquals' => true]]);
        $this->assertMatchesRegularExpression('/<option\s+value="notEquals"\s*>\s*is not on\s*</', $form);

        Post::factory()->create(['title' => 'Picked Day Article', 'published_at' => '2026-10-30 22:00:00']);
        Post::factory()->create(['title' => 'Other Day Article', 'published_at' => '2026-10-31 00:30:00']);

        $titles = $this->filteredValues(new Post, ['type' => 'date', 'valueFrom' => 'published_at', 'useTimezone' => false], ['condition' => 'notEquals', 'value' => '2026-10-30'], 'title');
        $this->assertSame(['Other Day Article'], $titles);
    }

    public function test_date_number_and_text_scopes_match_records_with_or_without_a_value(): void
    {
        Post::factory()->create(['title' => 'Dated Article', 'published_at' => '2026-10-30 10:00:00', 'excerpt' => 'A summary']);
        Post::factory()->create(['title' => 'Undated Article', 'published_at' => null, 'excerpt' => null]);
        Post::factory()->create(['title' => 'Blank Article', 'published_at' => null, 'excerpt' => '']);

        $date = ['type' => 'date', 'valueFrom' => 'published_at'];
        $this->assertSame(['Undated Article', 'Blank Article'], $this->filteredValues(new Post, $date, ['condition' => 'empty'], 'title'));
        $this->assertSame(['Dated Article'], $this->filteredValues(new Post, $date, ['condition' => 'notEmpty'], 'title'));

        // A text value is empty when it is missing or blank
        $text = ['type' => 'text', 'valueFrom' => 'excerpt'];
        $this->assertSame(['Undated Article', 'Blank Article'], $this->filteredValues(new Post, $text, ['condition' => 'empty'], 'title'));
        $this->assertSame(['Dated Article'], $this->filteredValues(new Post, $text, ['condition' => 'notEmpty'], 'title'));

        $counted = Sample::factory()->create(['units_sold' => 120]);
        $uncounted = Sample::factory()->create(['units_sold' => null]);
        $number = ['type' => 'number', 'valueFrom' => 'units_sold'];
        $this->assertSame([$uncounted->id], $this->filteredValues(new Sample, $number, ['condition' => 'empty'], 'id'));
        $this->assertSame([$counted->id], $this->filteredValues(new Sample, $number, ['condition' => 'notEmpty'], 'id'));
    }

    public function test_empty_conditions_take_no_input_and_label_the_chip(): void
    {
        $number = $this->renderScopeForm(['label' => 'Views', 'type' => 'number']);
        $this->assertMatchesRegularExpression('/<option\s+value="empty"\s*>\s*is empty\s*<\/option>\s*<option\s+value="notEmpty"\s*>\s*is not empty\s*<\/option>/', $number);
        $this->assertStringContainsString('data-trigger-condition="value[between][empty][notEmpty]"', $number);

        $text = $this->renderScopeForm(['label' => 'Title', 'type' => 'text']);
        $this->assertStringContainsString('data-trigger-condition="value[empty][notEmpty]"', $text);

        // A single empty condition shows its label alone
        $single = $this->renderScopeForm(['label' => 'Title', 'type' => 'text', 'conditions' => ['empty' => true]]);
        $this->assertStringContainsString('<input type="hidden" name="Filter[condition]" value="empty" />', $single);
        $this->assertStringNotContainsString('name="Filter[value]"', $single);

        $response = $this->ajax('textFilter::onFilterUpdate', ['scopeName' => 'title', 'Filter' => ['condition' => 'notEmpty', 'value' => '']]);
        $chip = collect($response->assertOk()->json('__ajax.ops'))->firstWhere('selector', '#Filter-textFilter-scope-title-group')['html'];
        $this->assertStringContainsString('Not empty', $chip);
    }

    public function test_the_trashed_scope_hides_deleted_comments_until_it_shows_them(): void
    {
        $post = Post::factory()->create();
        Comment::factory()->for($post)->create(['author' => 'Kept Author']);
        Comment::factory()->for($post)->create(['author' => 'Deleted Author'])->delete();

        $page = $this->get(route('reference.filters.scopes'))->assertOk()->getContent();
        preg_match('/id="Lists-trashedList".*?<\/table>/s', $page, $list);
        $this->assertStringContainsString('Kept Author', $list[0]);
        $this->assertStringNotContainsString('Deleted Author', $list[0]);

        $only = $this->ajax('trashedFilter::onFilterUpdate', ['scopeName' => 'deleted', 'value' => 'only']);
        $html = $this->listPatch($only, 'trashedList');
        $this->assertMatchesRegularExpression('/<tr class="deleted">.*?Deleted Author/s', $html);
        $this->assertStringNotContainsString('Kept Author', $html);

        $chip = collect($only->json('__ajax.ops'))->firstWhere('selector', '#Filter-trashedFilter-scope-deleted-group')['html'];
        $this->assertMatchesRegularExpression('/<span class="combobox-value filter-value">\s*Only\s*<\/span>/', $chip);

        $shown = $this->listPatch($this->ajax('trashedFilter::onFilterUpdate', ['scopeName' => 'deleted', 'value' => 'with']), 'trashedList');
        $this->assertMatchesRegularExpression('/<tr class="">.*?Kept Author/s', $shown);
        $this->assertMatchesRegularExpression('/<tr class="deleted">.*?Deleted Author/s', $shown);
    }

    public function test_the_trashed_scope_picks_from_a_menu_without_a_popover_form(): void
    {
        $html = $this->get(route('reference.filters.scopes'))->assertOk()->getContent();
        preg_match('/id="Filter-trashedFilter-scope-deleted-group".*?<\/div>\s*<\/div>/s', $html, $group);

        preg_match_all('/data-bs-value="(\w*)"\s*>\s*<span class="menu-item-content">\s*<span>\s*(\w+)\s*<\/span>/', $group[0], $items);
        $this->assertSame(['', 'with', 'only'], $items[1]);
        $this->assertSame(['Hidden', 'Shown', 'Only'], $items[2]);
        $this->assertStringNotContainsString('data-scope-form', $group[0]);
    }

    public function test_the_trashed_scope_needs_a_model_with_soft_deletes(): void
    {
        $this->expectException(SystemException::class);
        $this->expectExceptionMessage("The 'scoped' scope shows deleted records, so its model must use soft deletes.");

        $this->filteredValues(new Post, ['type' => 'trashed'], ['value' => 'with'], 'title');
    }

    public function test_the_text_scope_matches_part_of_the_value(): void
    {
        Post::factory()->create(['title' => 'Sorting Lists']);
        Post::factory()->create(['title' => 'Building Forms']);

        $html = $this->listPatch($this->ajax('textFilter::onFilterUpdate', [
            'scopeName' => 'title',
            'Filter' => ['condition' => 'contains', 'value' => 'list'],
        ]), 'textList');

        $this->assertStringContainsString('Sorting Lists', $html);
        $this->assertStringNotContainsString('Building Forms', $html);
    }

    public function test_the_text_scope_matches_the_whole_value(): void
    {
        Post::factory()->create(['title' => 'Sorting Lists']);
        Post::factory()->create(['title' => 'Sorting Lists Again']);

        $html = $this->listPatch($this->ajax('textFilter::onFilterUpdate', [
            'scopeName' => 'title',
            'Filter' => ['condition' => 'equals', 'value' => 'Sorting Lists'],
        ]), 'textList');

        $this->assertStringContainsString('Sorting Lists', $html);
        $this->assertStringNotContainsString('Sorting Lists Again', $html);
    }

    public function test_the_number_scope_matches_values_between_two_numbers(): void
    {
        Post::factory()->create(['title' => 'Quiet Article', 'views' => 10]);
        Post::factory()->create(['title' => 'Popular Article', 'views' => 500]);

        $html = $this->listPatch($this->ajax('numberFilter::onFilterUpdate', [
            'scopeName' => 'views',
            'Filter' => ['condition' => 'between', 'min' => '100', 'max' => '1000'],
        ]), 'numberList');

        $this->assertStringContainsString('Popular Article', $html);
        $this->assertStringNotContainsString('Quiet Article', $html);
    }

    public function test_the_numberrange_scope_with_one_end_becomes_greater(): void
    {
        $posts = Post::factory()->count(3)->sequence(fn ($sequence) => ['title' => 'Numbered '.($sequence->index + 1)])->create();

        $html = $this->listPatch($this->ajax('numberFilter::onFilterUpdate', [
            'scopeName' => 'id',
            'Filter' => ['condition' => 'between', 'min' => (string) $posts[1]->id, 'max' => ''],
        ]), 'numberList');

        $this->assertStringContainsString('Numbered 2', $html);
        $this->assertStringContainsString('Numbered 3', $html);
        $this->assertStringNotContainsString('Numbered 1<', $html);
    }

    /**
     * scopeForms returns the popover form each scope of a filter is rendered with, keyed by scope name.
     *
     * @return array<string, string>
     */
    protected function scopeForms(string $html, string $filterId): array
    {
        $document = \Dom\HTMLDocument::createFromString($html, LIBXML_NOERROR);
        $forms = [];

        foreach ($document->querySelectorAll("#{$filterId} .filter-group") as $group) {
            $link = $group->querySelector('[data-scope-name]');
            $template = $group->querySelector('template[data-scope-form]');

            if ($link && $template) {
                $forms[$link->getAttribute('data-scope-name')] = $template->innerHTML;
            }
        }

        return $forms;
    }

    public function test_each_widget_scope_is_rendered_with_its_popover_form(): void
    {
        $html = $this->get(route('reference.filters.scopes'))->assertOk()->getContent();
        $forms = $this->scopeForms($html, 'Filter-numberFilter');

        $this->assertEqualsCanonicalizing(['views', 'id'], array_keys($forms));

        // The number form carries the condition triggers the popover binds
        $this->assertStringContainsString('data-trigger="[name=\'Filter[condition]\']"', $forms['views']);
        $this->assertStringContainsString('data-filter-action="apply"', $forms['id']);
    }

    public function test_enter_in_a_number_date_or_text_form_applies_it_rather_than_clearing_it(): void
    {
        $html = $this->get(route('reference.filters.scopes'))->assertOk()->getContent();

        $forms = [
            $this->scopeForms($html, 'Filter-numberFilter')['views'],
            $this->scopeForms($html, 'Filter-dateFilter')['published_at'],
            $this->scopeForms($html, 'Filter-textFilter')['title'],
        ];

        // Enter submits with the form's first submit button, so Apply is the only one
        foreach ($forms as $form) {
            $this->assertMatchesRegularExpression('/<button type="button" class="[^"]*" data-filter-action="clear">/', $form);
            $this->assertMatchesRegularExpression('/<button type="submit" class="[^"]*" data-filter-action="apply">/', $form);
        }
    }

    public function test_scopes_without_a_popover_have_no_form_and_a_group_form_carries_its_first_options(): void
    {
        $guides = Category::factory()->create(['name' => 'Guides', 'slug' => 'guides']);
        Category::factory()->create(['name' => 'News', 'slug' => 'news']);

        $html = $this->get(route('reference.filters.scopes'))->assertOk()->getContent();
        $forms = $this->scopeForms($html, 'Filter-groupFilter');

        $this->assertEqualsCanonicalizing(['category', 'status'], array_keys($forms));
        $this->assertStringContainsString('data-control="groupfilter"', $forms['category']);

        preg_match('/data-options="([^"]*)"/', $forms['category'], $match);
        $options = json_decode(html_entity_decode($match[1]), true);

        $this->assertSame([], $options['active']);
        $this->assertEqualsCanonicalizing(['Guides', 'News'], array_column($options['available'], 'name'));
        $this->assertContains(['id' => $guides->id, 'name' => 'Guides'], $options['available']);
    }

    public function test_clear_filters_resets_every_scope_and_refreshes_the_list(): void
    {
        Post::factory()->published()->create(['title' => 'Live Article']);
        Post::factory()->create(['title' => 'Hidden Article']);

        $response = $this->withSession([
            'widget.app_controllers-FiltersController-Filter-switchFilter' => json_encode(['scope-published' => ['value' => '2']]),
        ])->ajax('switchFilter::onFilterClearAll');

        $html = $this->listPatch($response, 'switchList');
        $this->assertStringContainsString('Live Article', $html);
        $this->assertStringContainsString('Hidden Article', $html);
        $this->assertNotNull(collect($response->json('__ajax.ops'))->firstWhere('selector', '#Filter-switchFilter'));
    }
}
