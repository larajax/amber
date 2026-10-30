<?php

namespace Tests\Feature\Reference\Filters;

use App\Controllers\Reference\FiltersController;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Widgets\Filter;
use Tests\TestCase;

class OptionsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ajax posts an AJAX request for the given handler to the Filter Options page.
     *
     * @param  array<string, mixed>  $data
     */
    protected function ajax(string $handler, array $data = []): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.filters.options'), $data);
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
     * exampleHtml returns the markup of one example's filter and list.
     */
    protected function exampleHtml(string $alias): string
    {
        $html = $this->get(route('reference.filters.options'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/id="Filter-'.$alias.'Filter".*?id="Lists-'.$alias.'List".*?<\/table>/s', $html);
        preg_match('/id="Filter-'.$alias.'Filter".*?id="Lists-'.$alias.'List".*?<\/table>/s', $html, $match);

        return $match[0];
    }

    /**
     * renderDateScopeForm renders the popover form of a date scope with the given configuration.
     *
     * @param  array<string, mixed>  $config
     */
    protected function renderDateScopeForm(array $config): string
    {
        $filter = Filter::createIn(new FiltersController, [
            'model' => new Post,
            'scopes' => [
                'published_at' => $config + ['label' => 'Published', 'type' => 'date'],
            ],
        ]);

        $filter->bindToController();

        return (string) $filter->renderScopeFormElement('published_at');
    }

    public function test_the_page_renders_every_example_beside_its_yaml(): void
    {
        $html = $this->get(route('reference.filters.options'))->assertOk()->getContent();

        foreach (['defaults', 'conditions', 'empty-option', 'group-options', 'date-options', 'names', 'depends'] as $example) {
            $this->assertStringContainsString("resources/ui/reference/filters/options/{$example}.yaml", $html);
        }

        foreach (['defaults', 'conditions', 'emptyOption', 'groupOptions', 'dateOptions', 'names', 'depends'] as $alias) {
            $this->assertStringContainsString('id="Filter-'.$alias.'Filter"', $html);
            $this->assertStringContainsString('id="Lists-'.$alias.'List"', $html);
        }

        foreach (['makeOptionFilters', 'makeFilteredPosts', 'makeEmptyOptionFilter', 'makeEmptyOptionToolbar', 'makeDependsFilter', 'makeFilteredUsers'] as $method) {
            $this->assertStringContainsString("function {$method}(", $html);
        }
    }

    public function test_archived_posts_are_hidden_by_the_default_checkbox_scope(): void
    {
        Post::factory()->create(['title' => 'Current Article', 'status' => 'approved']);
        Post::factory()->create(['title' => 'Retired Article', 'status' => 'archived']);
        Post::factory()->create(['title' => 'Draft Article', 'status' => 'draft']);

        $html = $this->exampleHtml('defaults');

        $this->assertMatchesRegularExpression('/<input class="check check-sm" type="checkbox" id="Filter-defaultsFilter-scope-hide_archived" checked \/>/', $html);
        $this->assertStringContainsString('selected="selected"', $html);
        $this->assertStringContainsString('Current Article', $html);
        $this->assertStringNotContainsString('Retired Article', $html);
        $this->assertStringNotContainsString('Draft Article', $html);
    }

    public function test_clearing_the_default_checkbox_shows_archived_posts(): void
    {
        Post::factory()->create(['title' => 'Retired Article', 'status' => 'archived']);

        $html = $this->listPatch($this->withSession([
            'widget.app_controllers-FiltersController-Filter-defaultsFilter' => json_encode(['scope-status' => ['value' => '']]),
        ])->ajax('defaultsFilter::onFilterUpdate', ['scopeName' => 'hide_archived', 'value' => '0']), 'defaultsList');

        $this->assertStringContainsString('Retired Article', $html);
    }

    public function test_clear_filters_goes_back_to_the_defaults(): void
    {
        Post::factory()->create(['title' => 'Approved Article', 'status' => 'approved']);
        Post::factory()->create(['title' => 'Retired Article', 'status' => 'archived']);

        $html = $this->listPatch($this->withSession([
            'widget.app_controllers-FiltersController-Filter-defaultsFilter' => json_encode([
                'scope-hide_archived' => ['value' => '0'],
                'scope-status' => ['value' => ''],
            ]),
        ])->ajax('defaultsFilter::onFilterClearAll'), 'defaultsList');

        $this->assertStringContainsString('Approved Article', $html);
        $this->assertStringNotContainsString('Retired Article', $html);
    }

    public function test_the_dropdown_scope_runs_its_sql_condition(): void
    {
        Post::factory()->create(['title' => 'Popular Article', 'views' => 1500]);
        Post::factory()->create(['title' => 'Quiet Article', 'views' => 20]);

        $html = $this->listPatch($this->ajax('conditionsFilter::onFilterUpdate', ['scopeName' => 'popular', 'value' => '1000']), 'conditionsList');

        $this->assertStringContainsString('Popular Article', $html);
        $this->assertStringNotContainsString('Quiet Article', $html);
    }

    public function test_the_number_scope_runs_a_named_sql_condition(): void
    {
        $discussed = Post::factory()->create(['title' => 'Discussed Article']);
        Comment::factory()->count(3)->for($discussed)->create();
        Post::factory()->create(['title' => 'Silent Article']);

        $html = $this->listPatch($this->ajax('conditionsFilter::onFilterUpdate', [
            'scopeName' => 'comment_count',
            'Filter' => ['condition' => 'greater', 'value' => '2'],
        ]), 'conditionsList');

        $this->assertStringContainsString('Discussed Article', $html);
        $this->assertStringNotContainsString('Silent Article', $html);
    }

    public function test_scopes_only_offer_their_named_conditions(): void
    {
        $content = $this->ajax('conditionsFilter::onFilterUpdate', ['preload' => 'true'])->assertOk()->json('popoverContent');

        $this->assertStringContainsString('value="greater"', $content['comment_count']);
        $this->assertStringContainsString('value="lesser"', $content['comment_count']);
        $this->assertStringNotContainsString('value="between"', $content['comment_count']);
        $this->assertStringNotContainsString('value="equals"', $content['review_on']);
        $this->assertStringContainsString('<input type="hidden" name="Filter[condition]" value="contains" />', $content['slug']);
        $this->assertStringContainsString('<span>contains</span>', $content['slug']);
    }

    public function test_the_date_scope_runs_a_named_sql_condition(): void
    {
        Post::factory()->create(['title' => 'Due Soon Article', 'review_on' => '2026-11-02']);
        Post::factory()->create(['title' => 'Due Later Article', 'review_on' => '2027-02-01']);

        $html = $this->listPatch($this->ajax('conditionsFilter::onFilterUpdate', [
            'scopeName' => 'review_on',
            'Filter' => ['condition' => 'before', 'value' => '2026-12-01', 'valueRaw' => '2026-12-01'],
        ]), 'conditionsList');

        $this->assertStringContainsString('Due Soon Article', $html);
        $this->assertStringNotContainsString('Due Later Article', $html);
    }

    public function test_the_text_scope_with_one_condition_matches_part_of_the_value(): void
    {
        Post::factory()->create(['title' => 'Sorting Lists', 'slug' => 'sorting-lists']);
        Post::factory()->create(['title' => 'Building Forms', 'slug' => 'building-forms']);

        $html = $this->listPatch($this->ajax('conditionsFilter::onFilterUpdate', [
            'scopeName' => 'slug',
            'Filter' => ['condition' => 'contains', 'value' => 'lists'],
        ]), 'conditionsList');

        $this->assertStringContainsString('Sorting Lists', $html);
        $this->assertStringNotContainsString('Building Forms', $html);
    }

    public function test_the_empty_option_is_offered_first(): void
    {
        Tag::factory()->create(['name' => 'php']);

        $available = $this->ajax('emptyOptionFilterTags::onGetGroupOptions')->assertOk()->json('options.available');

        $this->assertSame(['id' => '', 'name' => 'Untagged'], $available[0]);
        $this->assertSame('php', $available[1]['name']);
    }

    public function test_the_tags_empty_option_matches_untagged_posts_and_keeps_the_scopes_before_it(): void
    {
        Post::factory()->published()->create(['title' => 'Untagged Live']);
        Post::factory()->create(['title' => 'Untagged Hidden']);
        $tagged = Post::factory()->published()->create(['title' => 'Tagged Live']);
        $tagged->tags()->attach(Tag::factory()->create());

        // The published switch comes before the tags scope, so the empty option must not widen it
        $html = $this->listPatch($this->withSession([
            'widget.app_controllers-FiltersController-Filter-emptyOptionFilter' => json_encode([
                'scope-published' => ['value' => '2'],
            ]),
        ])->ajax('emptyOptionFilter::onFilterUpdate', [
            'scopeName' => 'tags',
            'Filter' => ['value' => json_encode([''])],
        ]), 'emptyOptionList');

        $this->assertStringContainsString('Untagged Live', $html);
        $this->assertStringNotContainsString('Untagged Hidden', $html);
        $this->assertStringNotContainsString('Tagged Live', $html);
    }

    public function test_the_tags_empty_option_keeps_the_search_term(): void
    {
        Post::factory()->create(['title' => 'Untagged Banana']);
        Post::factory()->create(['title' => 'Untagged Cherry']);

        $html = $this->listPatch($this->withSession([
            'widget.app_controllers-FiltersController-Search-emptyOptionToolbarSearch' => json_encode(['term' => 'banana']),
        ])->ajax('emptyOptionFilter::onFilterUpdate', [
            'scopeName' => 'tags',
            'Filter' => ['value' => json_encode([''])],
        ]), 'emptyOptionList');

        $this->assertStringContainsString('Untagged Banana', $html);
        $this->assertStringNotContainsString('Untagged Cherry', $html);
    }

    public function test_the_group_options_only_offer_categories_with_posts(): void
    {
        $used = Category::factory()->create(['name' => 'Used Category', 'slug' => 'used-category']);
        Category::factory()->create(['name' => 'Empty Category', 'slug' => 'empty-category']);
        Post::factory()->for($used)->create();

        $names = collect($this->ajax('groupOptionsFilterCategory::onGetGroupOptions')->assertOk()->json('options.available'))
            ->pluck('name')
            ->all();

        $this->assertContains('Used Category', $names);
        $this->assertNotContains('Empty Category', $names);
    }

    public function test_the_toggle_match_mode_offers_include_and_exclude(): void
    {
        $content = $this->ajax('groupOptionsFilter::onLoadFilterForm', ['scopeName' => 'category'])->assertOk()->json('result');

        $this->assertStringContainsString('<div class="filter-mode">', $content);
        $this->assertStringContainsString('name="Filter[mode]" value="include" checked', $content);
        $this->assertStringContainsString('name="Filter[mode]" value="exclude"', $content);
    }

    public function test_the_exclude_mode_leaves_out_the_picked_categories(): void
    {
        $guides = Category::factory()->create(['name' => 'Guides', 'slug' => 'guides']);
        Post::factory()->for($guides)->create(['title' => 'Guide Article']);
        Post::factory()->create(['title' => 'Other Article']);

        $html = $this->listPatch($this->ajax('groupOptionsFilter::onFilterUpdate', [
            'scopeName' => 'category',
            'Filter' => ['value' => json_encode([$guides->id]), 'mode' => 'exclude'],
        ]), 'groupOptionsList');

        $this->assertStringContainsString('Other Article', $html);
        $this->assertStringNotContainsString('Guide Article', $html);
    }

    public function test_the_date_options_configure_the_pickers(): void
    {
        $content = $this->ajax('dateOptionsFilter::onLoadFilterForm', ['scopeName' => 'published_at'])->assertOk()->json('result');

        $this->assertSame(3, substr_count($content, 'data-first-day="1"'));
        $this->assertSame(3, substr_count($content, 'data-min-date="2026-01-01"'));
        $this->assertSame(3, substr_count($content, 'data-max-date="2026-12-31"'));
        $this->assertSame(3, substr_count($content, 'data-year-range="1"'));
        $this->assertSame(3, substr_count($content, 'data-show-week-number="true"'));
        $this->assertStringContainsString('&quot;useTimezone&quot;:false', $content);
    }

    public function test_date_filter_pickers_take_the_locale_first_day_unless_one_is_configured(): void
    {
        $default = $this->renderDateScopeForm([]);
        $this->assertSame(3, substr_count($default, 'data-control="datepicker"'));
        $this->assertStringNotContainsString('data-first-day', $default);

        $this->assertSame(3, substr_count($this->renderDateScopeForm(['firstDay' => 1]), 'data-first-day="1"'));
        $this->assertSame(3, substr_count($this->renderDateScopeForm(['firstDay' => 0]), 'data-first-day="0"'));
    }

    public function test_scope_names_labels_order_and_classes(): void
    {
        $html = $this->exampleHtml('names');

        $this->assertMatchesRegularExpression('/data-scope-name="live">\s*<input[^>]*>\s*<label[^>]*>\s*Live\s*<\/label>/', $html);
        $this->assertStringNotContainsString('Published posts only', $html);
        $this->assertMatchesRegularExpression('/data-scope-name="status".*data-scope-name="views"/s', $html);
        $this->assertStringContainsString('class="filter-group fst-italic"', $html);
    }

    public function test_value_from_filters_another_column(): void
    {
        Post::factory()->published()->create(['title' => 'Live Article']);
        Post::factory()->create(['title' => 'Hidden Article']);

        $html = $this->listPatch($this->ajax('namesFilter::onFilterUpdate', ['scopeName' => 'live', 'value' => '1']), 'namesList');

        $this->assertStringContainsString('Live Article', $html);
        $this->assertStringNotContainsString('Hidden Article', $html);
    }

    public function test_the_model_scope_filters_users_by_domain(): void
    {
        User::factory()->create(['name' => 'Alice', 'email' => 'alice@example.com']);
        User::factory()->create(['name' => 'Carol', 'email' => 'carol@example.org']);

        $html = $this->listPatch($this->ajax('dependsFilter::onFilterUpdate', ['scopeName' => 'domain', 'value' => 'example.org']), 'dependsList');

        $this->assertStringContainsString('carol@example.org', $html);
        $this->assertStringNotContainsString('alice@example.com', $html);
    }

    public function test_a_dependent_scope_resets_and_refreshes_when_its_dependency_changes(): void
    {
        User::factory()->create(['name' => 'Alice', 'email' => 'alice@example.com']);
        User::factory()->create(['name' => 'Bob', 'email' => 'bob@example.com']);
        User::factory()->create(['name' => 'Carol', 'email' => 'carol@example.org']);

        // The name scope depends on the domain scope and starts filtered to Alice
        $response = $this->withSession([
            'widget.app_controllers-FiltersController-Filter-dependsFilter' => json_encode([
                'scope-name' => ['value' => ['Alice' => 'Alice']],
            ]),
        ])->ajax('dependsFilter::onFilterUpdate', [
            'scopeName' => 'domain',
            'value' => 'example.com',
        ]);

        // The name scope is rendered again without its value
        $patch = collect($response->assertOk()->json('__ajax.ops'))->firstWhere('selector', '#Filter-dependsFilter-scope-name-group');
        $this->assertNotNull($patch);
        $this->assertStringContainsString('data-scope-name="name"', $patch['html']);
        $this->assertStringNotContainsString('filter-setting', $patch['html']);

        // The list drops the name filter and keeps the domain filter
        $html = $this->listPatch($response, 'dependsList');
        $this->assertStringContainsString('alice@example.com', $html);
        $this->assertStringContainsString('bob@example.com', $html);
        $this->assertStringNotContainsString('carol@example.org', $html);

        $store = json_decode(session('widget.app_controllers-FiltersController-Filter-dependsFilter'), true);
        $this->assertSame(['value' => 'example.com'], $store['scope-domain']);
        $this->assertNull($store['scope-name']);
    }
}
