<?php

namespace Tests\Feature\Reference\Lists;

use App\Models\Post;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Widgets\Lists;
use Tests\TestCase;

class ToolbarTest extends TestCase
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
     * ajax posts an AJAX request for the given handler to the Toolbar page.
     *
     * @param  array<string, mixed>  $data
     */
    protected function ajax(string $handler, array $data = []): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.lists.toolbar'), $data);
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
     * toolbarHtml returns the markup of one toolbar on the page.
     */
    protected function toolbarHtml(string $alias): string
    {
        $html = $this->get(route('reference.lists.toolbar'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/id="Toolbar-'.$alias.'".*?<!-- List Setup -->/s', $html);
        preg_match('/id="Toolbar-'.$alias.'".*?<!-- List Setup -->.*?<\/div>\s*<\/div>/s', $html, $match);

        return $match[0];
    }

    public function test_the_page_renders_every_example_with_its_source(): void
    {
        $html = $this->get(route('reference.lists.toolbar'))->assertOk()->getContent();

        foreach (['buttonsToolbar', 'partialToolbar', 'searchToolbar', 'overflowToolbar'] as $alias) {
            $this->assertStringContainsString('id="Toolbar-'.$alias.'"', $html);
        }

        foreach (['buttons.yaml', 'overflow.yaml', 'columns.yaml'] as $file) {
            $this->assertStringContainsString("resources/ui/reference/lists/toolbar/{$file}", $html);
        }

        $this->assertStringContainsString('resources/views/reference/lists/toolbar/_list_toolbar.php', $html);
        $this->assertStringContainsString('function makeButtonsToolbar(', $html);
        $this->assertStringContainsString('&lt;div data-control=&quot;drag-scroll&quot;&gt;', $html);
    }

    public function test_yaml_buttons_render_links_popups_hotkeys_and_checked_buttons(): void
    {
        $html = $this->toolbarHtml('buttonsToolbar');

        $this->assertStringContainsString('data-list-linkage="Lists-buttonsList"', $html);
        $this->assertStringContainsString('<div data-control="toolbar">', $html);
        $this->assertMatchesRegularExpression('/<a\s+href="'.preg_quote(url('posts/create'), '/').'"\s+class="btn-solid theme-primary"/', $html);
        $this->assertStringContainsString('data-control="popup"', $html);
        $this->assertStringContainsString('data-handler="onLoadToolbarDialog"', $html);
        $this->assertStringContainsString('data-hotkey="shift+p"', $html);
        $this->assertMatchesRegularExpression('/class="btn-solid theme-secondary"[^>]*data-request="onCountCheckedPosts"[^>]*data-request-flash="true"[^>]*data-list-checked-trigger="true"[^>]*data-list-checked-request="true"/', $html);
        $this->assertMatchesRegularExpression('/class="btn-solid theme-danger"[^>]*data-request="onDeleteCheckedPosts"[^>]*data-request-confirm="Delete the checked posts\?"/', $html);
    }

    public function test_the_partial_toolbar_renders_the_popup_hotkey_bulk_menu_and_counter(): void
    {
        $html = $this->toolbarHtml('partialToolbar');

        $this->assertStringContainsString('data-list-linkage="Lists-partialList"', $html);
        $this->assertStringContainsString('data-hotkey="shift+q"', $html);
        $this->assertStringContainsString('data-bs-toggle="menu"', $html);
        $this->assertMatchesRegularExpression('/data-bs-toggle="menu"[^>]*data-list-checked-trigger/', $html);
        $this->assertSame(3, substr_count($html, 'data-list-checked-request'));
        $this->assertStringContainsString('data-request="onDeleteCheckedPosts"', $html);
        $this->assertStringContainsString('data-request-confirm="Delete the checked posts?"', $html);
        $this->assertStringContainsString('<span data-list-checked-counter></span>', $html);
    }

    public function test_the_search_toolbar_has_a_search_box_and_the_setup_icon(): void
    {
        $html = $this->toolbarHtml('searchToolbar');

        $this->assertStringContainsString('id="Search-searchToolbarSearch"', $html);
        $this->assertStringContainsString('aria-label="Search posts..."', $html);
        $this->assertStringContainsString('data-handler="searchList::onLoadSetup"', $html);
        $this->assertStringContainsString('New Post', $html);
    }

    public function test_the_search_box_searches_the_bound_list(): void
    {
        Post::factory()->create(['title' => 'Banana Bread']);
        Post::factory()->create(['title' => 'Cherry Pie']);

        $this->ajax('searchToolbarSearch::onSubmit', ['searchToolbarSearch' => ['term' => 'banana']])
            ->assertOk()
            ->assertSee('Banana Bread')
            ->assertDontSee('Cherry Pie');
    }

    public function test_the_overflow_toolbar_sends_each_button_name(): void
    {
        $html = $this->toolbarHtml('overflowToolbar');

        $this->assertSame(8, substr_count($html, 'data-request="onToolbarButton"'));
        $this->assertStringContainsString('data-request-data="{&quot;button&quot;:&quot;export&quot;}"', $html);
    }

    public function test_the_toolbar_button_handler_names_the_button(): void
    {
        $this->assertFlash($this->ajax('onToolbarButton', ['button' => 'export']), 'info', 'The Export button was clicked.');
    }

    public function test_the_count_handler_counts_the_checked_posts_and_views(): void
    {
        $posts = Post::factory()->count(3)->sequence(['views' => 1000], ['views' => 250], ['views' => 5])->create();

        $this->assertFlash(
            $this->ajax('onCountCheckedPosts', ['checked' => [$posts[0]->id, $posts[1]->id]]),
            'info',
            '2 posts checked, with 1,250 views.'
        );
    }

    public function test_the_delete_handler_leaves_the_checked_posts_in_place(): void
    {
        $posts = Post::factory()->count(2)->create();

        $this->assertFlash(
            $this->ajax('onDeleteCheckedPosts', ['checked' => [$posts[0]->id]]),
            'warning',
            '1 post would be deleted. The reference pages leave the posts in place.'
        );

        $this->assertSame(2, Post::count());
    }

    public function test_the_refresh_handler_refreshes_the_list(): void
    {
        Post::factory()->create(['title' => 'Fresh Article']);

        $response = $this->ajax('onRefreshToolbarList');

        $this->assertFlash($response, 'info', 'The list was refreshed.');
        $patch = collect($response->json('__ajax.ops'))->firstWhere('selector', '#Lists-buttonsList');
        $this->assertNotNull($patch);
        $this->assertStringContainsString('Fresh Article', $patch['html']);
    }

    public function test_the_dialog_handler_returns_the_popup(): void
    {
        $content = $this->ajax('onLoadToolbarDialog')->assertOk()->json('result');

        $this->assertStringContainsString('onLoadToolbarDialog', $content);
        $this->assertStringContainsString('data-dismiss="popup"', $content);
    }
}
