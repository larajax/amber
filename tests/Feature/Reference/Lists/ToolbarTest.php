<?php

namespace Tests\Feature\Reference\Lists;

use App\Controllers\Reference\ListsController;
use App\Models\Post;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Widgets\Lists;
use Larajax\Ui\Widgets\Toolbar;
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

        foreach (['buttonsToolbar', 'partialToolbar', 'searchToolbar', 'overflowToolbar', 'menusToolbar', 'splitToolbar', 'groupedToolbar', 'extendedToolbar'] as $alias) {
            $this->assertStringContainsString('id="Toolbar-'.$alias.'"', $html);
        }

        foreach (['buttons.yaml', 'overflow.yaml', 'columns.yaml', 'menus.yaml', 'split.yaml', 'grouped.yaml', 'extend.yaml'] as $file) {
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

    public function test_yaml_menus_render_headers_dividers_submenus_hotkeys_and_checks(): void
    {
        $html = $this->toolbarHtml('menusToolbar');

        $this->assertMatchesRegularExpression('/data-bs-toggle="menu"[^>]*data-list-checked-trigger="true"/', $html);
        $this->assertMatchesRegularExpression('/<div class="menu-header">\s*Status\s*<\/div>/', $html);
        $this->assertSame(2, substr_count($html, '<hr class="menu-divider">'));
        $this->assertMatchesRegularExpression('/<div class="submenu">\s*<a class="menu-item" role="menuitem" href="javascript:;" aria-haspopup="true">.*?Export\s*<\/a>\s*<div class="menu control-dropdown" role="menu">.*?As CSV.*?As JSON/s', $html);
        $this->assertMatchesRegularExpression('/data-hotkey="shift\+x"[^>]*>\s*As CSV\s*<kbd class="menu-item-hotkey">⇧X<\/kbd>/', $html);
        $this->assertMatchesRegularExpression('/class="menu-item selected"[^>]*aria-current="true"[^>]*>\s*Newest first\s*<i class="ph ph-check menu-item-check"/', $html);
        $this->assertMatchesRegularExpression('/class="menu-item theme-danger"[^>]*data-request="onDeleteCheckedPosts"/', $html);

        // Every item of the checked menu sends the rows, the submenu's included, while the Sort items do not
        $this->assertSame(5, substr_count($html, 'data-list-checked-request'));
    }

    public function test_split_buttons_and_a_button_for_one_checked_row(): void
    {
        $html = $this->toolbarHtml('splitToolbar');

        $this->assertSame(2, substr_count($html, '<div class="btn-group split-button">'));
        $this->assertMatchesRegularExpression('/<a\s+href="'.preg_quote(url('posts/create'), '/').'"\s+class="btn-solid theme-primary"/', $html);
        $this->assertMatchesRegularExpression('/class="btn-solid theme-primary split-toggle"[^>]*data-bs-toggle="menu"[^>]*data-bs-placement="bottom-end"/', $html);
        $this->assertStringContainsString('data-control="popup" data-handler="onLoadToolbarDialog"', $html);
        $this->assertMatchesRegularExpression('/data-request="onCountCheckedPosts"[^>]*data-list-checked-trigger="true"[^>]*data-list-checked-request="true"/', $html);
        $this->assertMatchesRegularExpression('/theme-secondary split-toggle"[^>]*data-list-checked-trigger="true"/', $html);
        $this->assertMatchesRegularExpression('/data-request="onShowCheckedPost"[^>]*data-list-checked-trigger="single"/', $html);
    }

    public function test_groups_dividers_disabled_buttons_partials_and_end_buttons(): void
    {
        Post::factory()->count(2)->create(['published' => true]);

        $html = $this->toolbarHtml('groupedToolbar');

        $this->assertMatchesRegularExpression('/<div class="btn-group" role="group">\s*<button class="btn active"[^>]*data-request-data="\{&quot;button&quot;:&quot;list&quot;\}"[^>]*aria-pressed="true"/', $html);
        $this->assertSame(1, substr_count($html, 'aria-pressed="true"'));
        $this->assertStringContainsString('<span class="toolbar-divider"></span>', $html);
        $this->assertStringContainsString('<button class="btn" type="button" data-request="onToolbarButton" disabled="disabled">', $html);
        $this->assertMatchesRegularExpression('/<span class="button-text">\s*2 published\s*<\/span>/', $html);

        preg_match('/toolbar-primary">(.*?)<!-- End Buttons -->(.*)/s', $html, $cells);
        $this->assertStringNotContainsString('Help', $cells[1]);
        $this->assertMatchesRegularExpression('/<div class="toolbar-item toolbar-end">\s*<div class="btn-group">\s*<button[^>]*data-bs-placement="bottom-end"[^>]*>.*?Help/s', $cells[2]);
    }

    public function test_the_extend_event_places_and_removes_buttons_by_name(): void
    {
        $html = $this->toolbarHtml('extendedToolbar');

        $this->assertMatchesRegularExpression('/New Post.*?Preview.*?More.*?Publish.*?Feature/s', $html);
        $this->assertStringNotContainsString('Archive', $html);
        $this->assertStringNotContainsString('Actions', $html);
    }

    public function test_the_checked_menu_handler_names_the_item_and_counts_the_rows(): void
    {
        $this->assertFlash(
            $this->ajax('onCheckedToolbarButton', ['button' => 'csv export', 'checked' => [1, 2]]),
            'info',
            'Csv Export would apply to 2 checked posts.'
        );
    }

    public function test_the_show_handler_names_the_one_checked_post(): void
    {
        $post = Post::factory()->create(['title' => 'Only One']);

        $this->assertFlash($this->ajax('onShowCheckedPost', ['checked' => [$post->id]]), 'info', 'The checked post is "Only One".');
        $this->assertFlash($this->ajax('onShowCheckedPost'), 'info', 'No post is checked.');
    }

    public function test_hidden_items_leave_no_stray_dividers_headers_or_empty_menus(): void
    {
        $toolbar = $this->makeToolbar([
            'menu' => ['label' => 'Menu', 'items' => [
                'lead' => 'divider',
                'shown' => ['label' => 'Shown', 'handler' => 'onShown'],
                'twin' => 'divider',
                'pair' => 'divider',
                'orphan' => ['header' => 'Nothing below'],
                'hidden' => ['label' => 'Hidden', 'handler' => 'onHidden', 'visible' => false],
                'tail' => 'divider',
            ]],
            'labelled' => ['label' => 'Labelled', 'items' => [
                'status' => ['header' => 'Status'],
                'publish' => ['label' => 'Publish', 'handler' => 'onPublish'],
                'line' => 'divider',
                'more' => ['header' => 'More'],
                'archive' => ['label' => 'Archive', 'handler' => 'onArchive'],
            ]],
            'empty' => ['label' => 'Empty', 'items' => [
                'gone' => ['label' => 'Gone', 'handler' => 'onGone', 'visible' => false],
                'line' => 'divider',
            ]],
        ]);

        $this->assertSame(['menu', 'labelled'], array_keys($toolbar->getButtons()));
        $this->assertSame(['shown'], array_keys($toolbar->getVisibleItems($toolbar->getButton('menu'))));
        $this->assertSame(['status', 'publish', 'line', 'more', 'archive'], array_keys($toolbar->getVisibleItems($toolbar->getButton('labelled'))));
    }

    public function test_context_permissions_and_methods_apply_to_buttons_and_items(): void
    {
        $buttons = [
            'save' => ['label' => 'Save', 'handler' => 'onSave', 'context' => ['create', 'update']],
            'delete' => ['label' => 'Delete', 'handler' => 'onDelete', 'context' => 'update'],
            'menu' => ['label' => 'Menu', 'items' => [
                'restore' => ['label' => 'Restore', 'handler' => 'onRestore', 'context' => 'update'],
                'admin' => ['label' => 'Admin', 'handler' => 'onAdmin', 'permissions' => 'manage-everything'],
                'list' => ['label' => 'List', 'handler' => 'onList', 'active' => 'isToolbarViewActive'],
            ]],
            'any' => ['label' => 'Any', 'handler' => 'onAny'],
        ];

        $toolbar = $this->makeToolbar($buttons, 'create');

        $this->assertSame(['save', 'menu', 'any'], array_keys($toolbar->getButtons()));
        $this->assertSame(['list'], array_keys($toolbar->getVisibleItems($toolbar->getButton('menu'))));
        $this->assertTrue($toolbar->isButtonActive($toolbar->getButton('menu.list')));

        // A toolbar without a context hides the buttons that name one
        $this->assertSame(['menu', 'any'], array_keys($this->makeToolbar($buttons)->getButtons()));
    }

    public function test_add_buttons_places_new_buttons_by_name_and_dotted_path(): void
    {
        $toolbar = $this->makeToolbar([
            'first' => ['label' => 'First'],
            'menu' => ['label' => 'Menu', 'items' => [
                'a' => ['label' => 'A', 'handler' => 'onA'],
                'b' => ['label' => 'B', 'handler' => 'onB'],
            ]],
            'last' => ['label' => 'Last'],
        ]);

        $toolbar->addButtons(['before' => ['label' => 'Before']], before: 'menu');
        $toolbar->addButtons(['after' => ['label' => 'After']], after: 'menu');
        $toolbar->addButtons(['end' => ['label' => 'End']]);
        $toolbar->addButtons(['first' => ['label' => 'Replaced']]);
        $toolbar->addButtons(['between' => ['label' => 'Between', 'handler' => 'onBetween']], after: 'menu.a');
        $toolbar->removeButton('menu.b');

        $this->assertSame(['first', 'before', 'menu', 'after', 'last', 'end'], array_keys($toolbar->getButtons()));
        $this->assertSame('Replaced', $toolbar->getButton('first')->label);
        $this->assertSame(['a', 'between'], array_keys($toolbar->getButton('menu')->getItems()));
        $this->assertNull($toolbar->getButton('menu.missing'));
        $this->assertNull($toolbar->getButton('missing.item'));
    }

    public function test_disabled_links_outline_styles_and_a_dropdown_without_a_caret_render(): void
    {
        $html = $this->makeToolbar([
            'link' => ['label' => 'Link', 'url' => 'posts', 'disabled' => true],
            'outlined' => ['label' => 'Outlined', 'handler' => 'onOutlined', 'type' => 'danger', 'outline' => true],
            'more' => ['icon' => 'ph ph-dots-three', 'caret' => false, 'items' => [
                'a' => ['label' => 'A', 'url' => 'posts'],
            ]],
        ])->render();

        $this->assertMatchesRegularExpression('/<a\s+href="'.preg_quote(url('posts'), '/').'" class="btn disabled" type="button" aria-disabled="true">/', $html);
        $this->assertStringContainsString('<button class="btn-outline theme-danger" type="button" data-request="onOutlined">', $html);
        $this->assertStringContainsString('ph ph-dots-three', $html);
        $this->assertStringNotContainsString('ph-caret-down', $html);
    }

    /**
     * makeToolbar builds a toolbar of the given buttons on the Lists reference controller, which has the isToolbarViewActive method.
     *
     * @param  array<string, mixed>  $buttons
     */
    protected function makeToolbar(array $buttons, ?string $context = null): Toolbar
    {
        return Toolbar::createIn(new ListsController, ['alias' => 'apiToolbar', 'buttons' => $buttons, 'context' => $context]);
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
