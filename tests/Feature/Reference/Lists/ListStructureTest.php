<?php

namespace Tests\Feature\Reference\Lists;

use App\Controllers\Reference\ListsController;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Widgets\ListStructure;
use Tests\TestCase;

class ListStructureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ajax posts an AJAX request for the given handler to the List Structure page.
     *
     * @param  array<string, mixed>  $data
     */
    protected function ajax(string $handler, array $data = []): TestResponse
    {
        // Laravel keeps the route's controller across a test's requests, and the controller keeps its first AJAX handler
        app('router')->getRoutes()->getByName('reference.lists.list-structure')->flushController();

        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.lists.list-structure'), $data);
    }

    /**
     * makeTree creates a root category with a child and a grandchild.
     *
     * @return array{0: Category, 1: Category, 2: Category}
     */
    protected function makeTree(): array
    {
        $root = Category::factory()->create(['name' => 'Guides', 'slug' => 'guides']);
        $child = Category::factory()->childOf($root)->create(['name' => 'Forms', 'slug' => 'forms']);
        $grandchild = Category::factory()->childOf($child)->create(['name' => 'Validation Rules', 'slug' => 'validation-rules']);

        return [$root->refresh(), $child->refresh(), $grandchild->refresh()];
    }

    /**
     * structureHtml returns the markup of one structure on the page.
     */
    protected function structureHtml(string $alias): string
    {
        $html = $this->get(route('reference.lists.list-structure'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/id="ListStructure-'.$alias.'".*?<\/table>/s', $html);
        preg_match('/id="ListStructure-'.$alias.'".*?<\/table>/s', $html, $match);

        return $match[0];
    }

    public function test_the_page_renders_every_example_with_its_method_and_yaml(): void
    {
        $html = $this->get(route('reference.lists.list-structure'))->assertOk()->getContent();

        foreach (['treeList', 'checkboxTree', 'browseTree', 'reorderList'] as $alias) {
            $this->assertStringContainsString('id="ListStructure-'.$alias.'"', $html);
        }

        foreach (['makeTreeList', 'makeTreeToolbar', 'makeCheckboxTree', 'makeCheckboxTreeToolbar', 'onShowCheckedCategories', 'makeBrowseTree', 'makeReorderList'] as $method) {
            $this->assertStringContainsString("function {$method}(", $html);
        }

        $this->assertStringContainsString('resources/ui/reference/lists/list-structure/categories.yaml', $html);
        $this->assertStringContainsString('resources/ui/reference/lists/list-structure/users.yaml', $html);
        $this->assertStringContainsString('ui-example-preview reference-scroll-preview', $html);
    }

    public function test_the_tree_cell_carries_its_level_and_indent_for_the_guides(): void
    {
        $this->makeTree();

        $html = $this->structureHtml('treeList');

        // A level is 18 pixels, after 15 for the tree's toggles
        $this->assertMatchesRegularExpression('/<td class="[^"]*list-cell-tree[^"]*" style="--tree-level:0;--tree-pad:15px;padding-left:15px"/', $html);
        $this->assertMatchesRegularExpression('/<td class="[^"]*list-cell-tree[^"]*" style="--tree-level:2;--tree-pad:51px;padding-left:51px"/', $html);
    }

    public function test_the_checkbox_tree_puts_a_checkbox_before_the_drag_handle_and_sends_the_checked_rows(): void
    {
        [$root, $child] = $this->makeTree();

        $html = $this->structureHtml('checkboxTree');

        $this->assertMatchesRegularExpression('/class="control-list list-scrollable list-checkboxes"\s+data-control="listwidget liststructurewidget"/', $html);
        $this->assertMatchesRegularExpression('/<td class="list-checkbox[^"]*"[^>]*>.*?<\/td>\s*<td class="list-reorder nolink">/s', $html);

        $this->ajax('onShowCheckedCategories', ['checked' => [$child->id, $root->id]])
            ->assertOk()
            ->assertJsonPath('__ajax.ops.0.op', 'flash')
            ->assertJsonPath('__ajax.ops.0.text', '2 categories checked: Forms, Guides.');
    }

    public function test_the_category_tree_renders_three_levels(): void
    {
        [$root] = $this->makeTree();

        $html = $this->structureHtml('treeList');

        $this->assertStringContainsString('data-control="listwidget liststructurewidget"', $html);
        $this->assertStringContainsString('data-reorder-handler="treeList::onReorder"', $html);
        $this->assertStringContainsString('data-use-reorder="1"', $html);
        $this->assertStringContainsString('data-use-tree="1"', $html);
        $this->assertStringContainsString('data-max-depth="3"', $html);
        $this->assertStringContainsString('data-drag-row="1"', $html);
        $this->assertStringContainsString('data-tree-id="'.$root->id.'"', $html);
        $this->assertStringContainsString('data-tree-level="2"', $html);
        $this->assertStringContainsString('tree-expand-collapse is-expanded', $html);
        $this->assertStringContainsString('list-reorder-handle', $html);
        $this->assertMatchesRegularExpression('/Guides.*Forms.*Validation Rules/s', $html);
    }

    public function test_the_tree_counts_the_posts_of_each_category(): void
    {
        [$root] = $this->makeTree();
        Post::factory()->count(3)->for($root)->create();

        $this->assertMatchesRegularExpression('/list-cell-name-posts_count list-cell-type-number\s+"[^>]*>\s+3\s+</', $this->structureHtml('treeList'));
    }

    public function test_the_browsing_tree_starts_collapsed_without_reordering(): void
    {
        [$root] = $this->makeTree();

        $html = $this->structureHtml('browseTree');

        $this->assertStringContainsString('data-use-reorder=""', $html);
        $this->assertStringNotContainsString('list-reorder-handle', $html);
        $this->assertStringContainsString('data-tree-id="'.$root->id.'"', $html);
        $this->assertStringContainsString('data-tree-expanded="false"', $html);
        $this->assertStringNotContainsString('Forms', $html);
    }

    public function test_the_reorder_list_is_flat_and_sends_sort_orders(): void
    {
        User::factory()->count(2)->create();

        $html = $this->structureHtml('reorderList');

        $this->assertStringContainsString('data-use-tree=""', $html);
        $this->assertStringContainsString('data-drag-row=""', $html);
        $this->assertStringContainsString('data-include-sort-orders="1"', $html);
        $this->assertStringContainsString('list-cell-tree', $html);
        $this->assertStringNotContainsString('tree-expand-collapse', $html);
    }

    public function test_the_tree_toolbar_searches_the_tree_as_a_flat_list(): void
    {
        $this->makeTree();
        Category::factory()->create(['name' => 'News', 'slug' => 'news']);

        $html = $this->ajax('treeToolbarSearch::onSubmit', ['treeToolbarSearch' => ['term' => 'validation']])
            ->assertOk()
            ->json('__ajax.ops.0.html');

        $this->assertStringContainsString('data-use-tree=""', $html);
        $this->assertStringContainsString('Validation Rules', $html);
        $this->assertStringNotContainsString('News', $html);
    }

    public function test_on_reorder_nests_a_category_under_another(): void
    {
        [, $child, $grandchild] = $this->makeTree();
        $other = Category::factory()->create(['name' => 'News', 'slug' => 'news']);

        $this->ajax('treeList::onReorder', [
            'record_id' => $grandchild->id,
            'parent_id' => $other->id,
        ])->assertOk();

        $this->assertSame($other->id, $grandchild->refresh()->parent_id);
        $this->assertSame(0, $child->refresh()->getChildCount());
    }

    public function test_on_reorder_moves_a_category_after_a_sibling(): void
    {
        $first = Category::factory()->create(['name' => 'First', 'slug' => 'first']);
        $second = Category::factory()->create(['name' => 'Second', 'slug' => 'second']);

        // A sortable model gets the ids of every row in their new order
        $this->ajax('treeList::onReorder', [
            'record_id' => $first->id,
            'previous_id' => $second->id,
            'sort_orders' => [$second->id, $first->id],
        ])->assertOk();

        $this->assertSame(['Second', 'First'], Category::orderBy('sort_order')->pluck('name')->all());
    }

    public function test_on_toggle_tree_node_collapses_a_branch(): void
    {
        [$root] = $this->makeTree();

        $html = $this->ajax('treeList::onToggleTreeNode', [
            'node_id' => $root->id,
            'status' => 1,
        ])->assertOk()->json('__ajax.ops.0.html');

        $this->assertStringContainsString('data-tree-id="'.$root->id.'"', $html);
        $this->assertStringContainsString('data-tree-expanded="false"', $html);
        $this->assertStringNotContainsString('Validation Rules', $html);
    }

    public function test_sorting_a_column_shows_a_flat_list_with_the_structure_icon(): void
    {
        $this->makeTree();

        $html = $this->ajax('treeList::onSort', ['sortColumn' => 'name'])->assertOk()->json('__ajax.ops.0.html');

        $this->assertStringContainsString('data-use-tree=""', $html);
        $this->assertStringContainsString('Validation Rules', $html);
        $this->assertStringContainsString('data-request="treeList::onShowStructure"', $html);
    }

    public function test_a_sorted_tree_offers_a_labelled_way_back_to_the_tree(): void
    {
        $this->makeTree();

        $html = $this->ajax('treeList::onSort', ['sortColumn' => 'name'])->assertOk()->json('__ajax.ops.0.html');

        // The last head cell holds it, with no column of its own
        $this->assertMatchesRegularExpression('/<th style="" class="list-cell-name-posts_count[^"]*has-show-structure[^"]*">\s*<button\s+type="button"\s+class="list-show-structure"\s+data-request="treeList::onShowStructure"\s*>\s*<i class="ph ph-tree-structure" aria-hidden="true"><\/i>\s*<span>\s*Show tree\s*<\/span>\s*<\/button>\s*<a/', $html);

        preg_match('/<thead>.*?<\/thead>/s', $html, $head);
        preg_match('/<tr\s+class="[^"]*"\s+data-tree-id=.*?<\/tr>/s', $html, $row);
        $this->assertSame(preg_match_all('/<th[\s>]/', $head[0]), preg_match_all('/<td[\s>]/', $row[0]));
        $this->assertStringNotContainsString('list-setup', $html);
    }

    public function test_a_sorted_reorder_list_offers_its_way_back_to_the_order(): void
    {
        User::factory()->count(2)->create();

        $html = $this->ajax('reorderList::onSort', ['sortColumn' => 'name'])->assertOk()->json('__ajax.ops.0.html');

        $this->assertStringContainsString('data-request="reorderList::onShowStructure"', $html);
        $this->assertMatchesRegularExpression('/<span>\s*Show order\s*<\/span>/', $html);
    }

    public function test_a_searched_tree_offers_no_way_back_until_the_search_is_cleared(): void
    {
        $this->makeTree();

        $html = $this->ajax('treeToolbarSearch::onSubmit', ['treeToolbarSearch' => ['term' => 'validation']])
            ->assertOk()
            ->json('__ajax.ops.0.html');

        $this->assertStringNotContainsString('list-show-structure', $html);
        $this->assertStringNotContainsString('has-show-structure', $html);
    }

    public function test_on_reorder_stores_the_new_user_order(): void
    {
        $users = User::factory()->count(3)->create();

        $this->ajax('reorderList::onReorder', [
            'record_id' => $users[2]->id,
            'next_id' => $users[0]->id,
            'sort_orders' => [$users[2]->id, $users[0]->id, $users[1]->id],
        ])->assertOk();

        $this->assertSame(
            [$users[2]->id, $users[0]->id, $users[1]->id],
            User::orderBy('sort_order')->pluck('id')->all()
        );
    }

    public function test_the_structure_reads_include_reference_pool_from_config(): void
    {
        $structure = ListStructure::createIn(new ListsController, [
            'model' => new User,
            'columns' => ['name' => ['label' => 'Name']],
            'showTree' => false,
            'includeReferencePool' => true,
        ]);

        $this->assertTrue($structure->includeReferencePool);
    }
}
