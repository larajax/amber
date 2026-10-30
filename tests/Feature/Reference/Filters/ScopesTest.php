<?php

namespace Tests\Feature\Reference\Filters;

use App\Controllers\Reference\FiltersController;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Widgets\Filter;
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

    public function test_the_page_renders_every_example_beside_its_yaml(): void
    {
        $html = $this->get(route('reference.filters.scopes'))->assertOk()->getContent();

        foreach (['checkbox', 'switch', 'dropdown', 'group', 'date', 'text', 'number'] as $example) {
            $this->assertStringContainsString("resources/ui/reference/filters/scopes/{$example}.yaml", $html);
            $this->assertStringContainsString('id="Filter-'.$example.'Filter"', $html);
            $this->assertStringContainsString('id="Lists-'.$example.'List"', $html);
        }

        $this->assertStringContainsString('function makeScopeExamples(', $html);
        $this->assertStringContainsString('resources/ui/reference/filters/posts.yaml', $html);
        $this->assertStringContainsString('data-request="checkboxFilter::onFilterClearAll"', $html);
    }

    public function test_each_scope_type_renders_its_control(): void
    {
        $html = $this->get(route('reference.filters.scopes'))->assertOk()->getContent();

        $this->assertStringContainsString('class="filter-scope scope-checkbox form-field"', $html);
        $this->assertMatchesRegularExpression('/class="filter-scope form-field is-indeterminate"\s+data-scope-name="published"/', $html);
        $this->assertStringContainsString('data-icon="ph ph-pencil-simple"', $html);
        $this->assertStringContainsString('>Any status</option>', $html);

        foreach (['category', 'published_at', 'review_on', 'title', 'views', 'id'] as $scopeName) {
            $this->assertMatchesRegularExpression('/<a\s+href="javascript:;"\s+class="filter-scope "\s+data-scope-name="'.$scopeName.'"/', $html);
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
        $this->assertStringContainsString('<span class="filter-setting">1</span>', $scope['html']);
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

    public function test_the_group_search_is_an_adorned_field_with_a_search_icon(): void
    {
        $html = $this->ajax('groupFilter::onLoadFilterForm', ['scopeName' => 'status'])->assertOk()->json('result');

        $this->assertMatchesRegularExpression('/<div class="filter-search loading-indicator-container size-input-text">\s*<label class="form-control form-control-sm form-adorn">\s*<span class="form-adorn-icon" aria-hidden="true">\s*<i class="ph ph-magnifying-glass" aria-hidden="true"><\/i>\s*<\/span>\s*<input[^>]*aria-label="Search..."[^>]*class="filter-search-input form-ghost popup-allow-focus"[^>]*data-track-input \/>\s*<\/label>/', $html);
        $this->assertStringNotContainsString('search-input-container', $html);
    }

    public function test_the_date_filter_form_posts_the_raw_date_inputs(): void
    {
        $content = $this->ajax('dateFilter::onLoadFilterForm', ['scopeName' => 'published_at'])->assertOk()->json('result');

        // The date widget only applies when the raw inputs are posted
        $this->assertStringContainsString('Filter[valueRaw]', $content);
        $this->assertStringContainsString('Filter[afterRaw]', $content);
        $this->assertStringContainsString('Filter[beforeRaw]', $content);
        $this->assertStringContainsString('<option', $content);
        $this->assertStringContainsString('value="notEquals"', $content);
    }

    public function test_date_filter_inputs_sit_in_small_adorned_fields_with_a_calendar_icon(): void
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

    public function test_preloading_returns_the_popover_form_of_each_widget_scope(): void
    {
        $content = $this->ajax('numberFilter::onFilterUpdate', ['preload' => 'true'])->assertOk()->json('popoverContent');

        $this->assertEqualsCanonicalizing(['views', 'id'], array_keys($content));

        // The number form carries the condition triggers the popover binds
        $this->assertStringContainsString('data-trigger="[name=\'Filter[condition]\']"', $content['views']);
        $this->assertStringContainsString('data-filter-action="apply"', $content['id']);
    }

    public function test_preloading_skips_the_scopes_without_a_popover(): void
    {
        $content = $this->ajax('groupFilter::onFilterUpdate', ['preload' => 'true'])->assertOk()->json('popoverContent');

        $this->assertEqualsCanonicalizing(['category', 'status'], array_keys($content));
        $this->assertStringContainsString('data-control="groupfilter"', $content['category']);
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
