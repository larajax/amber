<?php

namespace Tests\Feature\Reference\Lists;

use App\Models\Category;
use App\Models\Post;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Widgets\Lists;
use Tests\TestCase;

class OptionsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * tearDown clears the list preferences, which the widget caches statically for the whole process.
     */
    protected function tearDown(): void
    {
        Closure::bind(fn () => static::$preferenceCache = [], null, Lists::class)();

        parent::tearDown();
    }

    /**
     * ajax posts an AJAX request for the given handler to the List Options page.
     *
     * @param  array<string, mixed>  $data
     */
    protected function ajax(string $handler, array $data = []): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.lists.options'), $data);
    }

    /**
     * assertFlash checks the response carries a flash message with the given level and text.
     */
    protected function assertFlash(TestResponse $response, string $level, string $text): void
    {
        $response->assertOk();

        $flash = collect($response->json('__ajax.ops'))->firstWhere('op', 'flash');

        $this->assertNotNull($flash, 'The response has no flash message.');
        $this->assertSame($level, $flash['level']);
        $this->assertSame($text, $flash['text']);
    }

    /**
     * listHtml returns the markup of one list on the page.
     */
    protected function listHtml(string $html, string $alias): string
    {
        $this->assertMatchesRegularExpression('/id="Lists-'.$alias.'".*?<\/table>/s', $html);
        preg_match('/id="Lists-'.$alias.'".*?<\/table>/s', $html, $match);

        return $match[0];
    }

    public function test_the_page_renders_every_example_with_its_method_and_yaml(): void
    {
        $html = $this->get(route('reference.lists.options'))->assertOk()->getContent();

        foreach (['sortingList', 'pagedList', 'simplePagedList', 'setupList', 'checkboxList', 'searchableList', 'recordUrlList', 'popupList', 'rowEventsList'] as $alias) {
            $this->assertStringContainsString('id="Lists-'.$alias.'"', $html);
            $this->assertStringContainsString('alias: '.e("'{$alias}'").',', $html);
        }

        foreach (['sorting', 'posts', 'setup', 'searchable', 'record-url', 'popup'] as $file) {
            $this->assertStringContainsString("resources/ui/reference/lists/options/{$file}.yaml", $html);
        }

        $this->assertStringContainsString('app/Controllers/Reference/ListsController/OptionsExamples.php', $html);
        $this->assertStringContainsString('&lt;table class=&quot;table data&quot; data-control=&quot;rowlink&quot;&gt;', $html);
    }

    public function test_the_list_starts_with_the_default_sort(): void
    {
        Post::factory()->create(['title' => 'Quiet Article', 'views' => 10]);
        Post::factory()->create(['title' => 'Popular Article', 'views' => 900]);

        $html = $this->listHtml($this->get(route('reference.lists.options'))->getContent(), 'sortingList');

        $this->assertMatchesRegularExpression('/Popular Article.*Quiet Article/s', $html);
        $this->assertStringContainsString('list-cell-name-views list-cell-type-number   sort-desc active', $html);
        $this->assertMatchesRegularExpression('/<th style="" class="list-cell-name-excerpt list-cell-type-summary  ">\s*<span>Excerpt<\/span>/', $html);
    }

    public function test_sorting_a_column_reverses_and_remembers_the_order(): void
    {
        Post::factory()->create(['title' => 'Alpha Article', 'views' => 10]);
        Post::factory()->create(['title' => 'Zulu Article', 'views' => 900]);

        $html = $this->ajax('sortingList::onSort', ['sortColumn' => 'title'])->assertOk()->json('__ajax.ops.0.html');

        $this->assertMatchesRegularExpression('/Zulu Article.*Alpha Article/s', $html);
        $this->assertStringContainsString('"sort":{"column":"title","direction":"desc"', session('widget.app_controllers-ListsController-Lists-sortingList'));
    }

    public function test_pagination_names_the_page_in_the_address(): void
    {
        Post::factory()->count(7)->create();

        $html = $this->get(route('reference.lists.options'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/data-request="pagedList::onPaginate"\s+data-request-query="\{ postsPage: 2 \}"/', $html);
        $this->assertStringContainsString('records 1 to 5 of 7', $html);
        $this->assertStringContainsString('id="Lists-pagedList-pageChooserPopover"', $html);
    }

    public function test_the_page_name_picks_the_page_on_load(): void
    {
        Post::factory()->count(7)->sequence(fn ($sequence) => ['title' => 'Numbered '.($sequence->index + 1)])->create();

        $html = $this->listHtml($this->get(route('reference.lists.options', ['postsPage' => 2]))->assertOk()->getContent(), 'pagedList');

        $this->assertStringContainsString('Numbered 6', $html);
        $this->assertStringNotContainsString('Numbered 1<', $html);
    }

    public function test_simple_pagination_leaves_out_the_count_and_page_links(): void
    {
        Post::factory()->count(7)->create();

        $html = $this->get(route('reference.lists.options'))->assertOk()->getContent();

        preg_match('/id="Lists-simplePagedList".*?<\/nav>/s', $html, $match);
        $this->assertStringNotContainsString('list-pagination-summary', $match[0]);
        $this->assertStringContainsString('class="page-link page-active"', $match[0]);
        $this->assertMatchesRegularExpression('/data-request-data="\{ _page: 2 \}"\s+data-request="simplePagedList::onPaginate"/', $match[0]);
    }

    public function test_the_setup_toolbar_opens_the_setup_of_its_list(): void
    {
        $html = $this->get(route('reference.lists.options'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/id="Toolbar-setupToolbar".*?data-control="popup"\s+data-handler="setupList::onLoadSetup"/s', $html);
    }

    public function test_the_setup_popup_offers_the_columns_and_page_sizes(): void
    {
        $content = $this->ajax('setupList::onLoadSetup')->assertOk()->json('result');

        $this->assertStringContainsString('data-control="simplelist"', $content);
        $this->assertStringContainsString('name="column_order[]"', $content);
        $this->assertMatchesRegularExpression('/value="excerpt"\s+type="checkbox"/', $content);
        $this->assertMatchesRegularExpression('/value="title"\s+checked="checked"/', $content);
        $this->assertStringNotContainsString('value="id"', $content);
        $this->assertStringContainsString('<option value="5" selected="selected">', $content);
        $this->assertStringContainsString('<option value="20" >', $content);
    }

    public function test_applying_the_setup_shows_an_invisible_column_and_the_page_size(): void
    {
        Post::factory()->count(12)->create(['excerpt' => 'A summary that should now show.']);

        $html = $this->ajax('setupList::onApplySetup', [
            'visible_columns' => ['excerpt', 'title'],
            'column_order' => ['excerpt', 'title'],
            'records_per_page' => 10,
        ])->assertOk()->json('__ajax.ops.0.html');

        $this->assertStringContainsString('list-cell-index-1 list-cell-name-excerpt', $html);
        $this->assertStringNotContainsString('list-cell-name-status', $html);
        $this->assertStringContainsString('records 1 to 10 of 12', $html);
    }

    public function test_the_checkbox_toolbar_sends_the_checked_rows(): void
    {
        Post::factory()->create();

        $html = $this->get(route('reference.lists.options'))->assertOk()->getContent();
        $this->assertStringContainsString('<th class="list-checkbox">', $this->listHtml($html, 'checkboxList'));
        $this->assertMatchesRegularExpression('/data-list-linkage="Lists-checkboxList"/', $html);
        $this->assertMatchesRegularExpression('/<button[^>]*data-request="onShowCheckedPosts"[^>]*data-request-flash="true"[^>]*data-list-checked-trigger="true"[^>]*data-list-checked-request="true"/', $html);
    }

    public function test_the_checked_posts_handler_names_the_checked_rows(): void
    {
        Post::factory()->create(['title' => 'First Checked']);
        Post::factory()->create(['title' => 'Unchecked']);
        $second = Post::factory()->create(['title' => 'Second Checked']);

        $this->assertFlash(
            $this->ajax('onShowCheckedPosts', ['checked' => [1, $second->id]]),
            'info',
            '2 posts checked: First Checked, Second Checked.'
        );
    }

    public function test_searchable_columns_include_relations_and_invisible_columns(): void
    {
        Post::factory()->create(['title' => 'Nested Data', 'excerpt' => 'Plain words.']);
        Post::factory()->for(Category::factory()->create(['name' => 'Releases', 'slug' => 'releases']))->create(['title' => 'Version Notes']);
        Post::factory()->create(['title' => 'Hidden Match', 'excerpt' => 'A zebra appears.']);

        $this->ajax('searchableToolbarSearch::onSubmit', ['searchableToolbarSearch' => ['term' => 'releases']])
            ->assertOk()
            ->assertSee('Version Notes')
            ->assertDontSee('Nested Data');

        $this->ajax('searchableToolbarSearch::onSubmit', ['searchableToolbarSearch' => ['term' => 'zebra']])
            ->assertOk()
            ->assertSee('Hidden Match')
            ->assertDontSee('list-cell-name-excerpt', false);
    }

    public function test_a_search_without_matches_shows_the_no_records_message(): void
    {
        Post::factory()->create();

        $this->ajax('searchableToolbarSearch::onSubmit', ['searchableToolbarSearch' => ['term' => 'nothing-like-this']])
            ->assertOk()
            ->assertSee('No posts match the search.');
    }

    public function test_record_url_links_rows_except_cells_that_are_not_clickable(): void
    {
        $post = Post::factory()->published()->create();

        $html = $this->listHtml($this->get(route('reference.lists.options'))->getContent(), 'recordUrlList');

        $this->assertStringContainsString('data-control="rowlink"', $html);
        $this->assertStringContainsString('<a  href="'.url("posts/{$post->id}/edit").'">', $html);
        $this->assertMatchesRegularExpression('/list-cell-name-published list-cell-type-switch\s+nolink/', $html);
        $this->assertMatchesRegularExpression('/list-cell-name-category list-cell-type-linkage\s+nolink/', $html);
    }

    public function test_record_click_opens_the_record_in_a_popup_and_lists_every_record(): void
    {
        $category = Category::factory()->create(['name' => 'Guides', 'slug' => 'guides']);
        Category::factory()->count(25)->create();

        $html = $this->get(route('reference.lists.options'))->getContent();
        $listHtml = $this->listHtml($html, 'popupList');

        $this->assertStringContainsString("onclick=\"jax.listOnLoadForm(&#039;{$category->id}&#039;)\"", $listHtml);
        $this->assertSame(26, substr_count($listHtml, 'onclick="jax.listOnLoadForm'));
        $this->assertDoesNotMatchRegularExpression('/id="Lists-popupList".*?list-footer.*?id="Lists-rowEventsList"/s', $html);
    }

    public function test_the_popup_handler_shows_the_category_and_its_posts(): void
    {
        $category = Category::factory()->create(['name' => 'Guides', 'slug' => 'guides', 'description' => 'All the guides.']);
        Post::factory()->for($category)->create(['title' => 'Guide Article']);

        $content = $this->ajax('onLoadPopupForm', ['form_record_id' => $category->id])->assertOk()->json('result');

        $this->assertStringContainsString('Guides', $content);
        $this->assertStringContainsString('All the guides.', $content);
        $this->assertStringContainsString('Guide Article', $content);
        $this->assertStringContainsString('data-dismiss="popup"', $content);
    }

    public function test_row_events_fade_and_unlink_archived_posts(): void
    {
        $live = Post::factory()->create(['title' => 'Live Article', 'status' => 'approved']);
        $archived = Post::factory()->create(['title' => 'Old Article', 'status' => 'archived']);

        $html = $this->listHtml($this->get(route('reference.lists.options'))->getContent(), 'rowEventsList');

        $this->assertStringContainsString('<tr class="opacity-50">', $html);
        $this->assertStringContainsString('href="'.url("posts/{$live->id}/edit").'"', $html);
        $this->assertStringNotContainsString('href="'.url("posts/{$archived->id}/edit").'"', $html);
    }

    public function test_the_rowlink_table_links_rows_to_a_page_a_popup_and_a_handler(): void
    {
        $html = $this->get(route('reference.lists.options'))->assertOk()->getContent();

        $this->assertStringContainsString('<table class="table data" data-control="rowlink">', $html);
        $this->assertStringContainsString('href="'.route('reference.lists.columns').'"', $html);
        $this->assertStringContainsString('data-control="popup" data-handler="onLoadRowDialog"', $html);
        $this->assertStringContainsString('data-request="onRowAction" data-request-flash', $html);
        $this->assertStringContainsString('<td class="nolink">', $html);
    }

    public function test_the_row_dialog_handler_returns_the_dialog(): void
    {
        $this->assertStringContainsString('onLoadRowDialog', $this->ajax('onLoadRowDialog')->assertOk()->json('result'));
    }

    public function test_the_row_action_handler_flashes_a_message(): void
    {
        $this->assertFlash($this->ajax('onRowAction'), 'info', 'The row ran an AJAX handler.');
    }
}
