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

        foreach (['treeList', 'browseTree', 'reorderList'] as $alias) {
            $this->assertStringContainsString('id="ListStructure-'.$alias.'"', $html);
        }

        foreach (['makeTreeList', 'makeTreeToolbar', 'makeBrowseTree', 'makeReorderList'] as $method) {
            $this->assertStringContainsString("function {$method}(", $html);
        }

        $this->assertStringContainsString('resources/ui/reference/lists/list-structure/categories.yaml', $html);
        $this->assertStringContainsString('resources/ui/reference/lists/list-structure/users.yaml', $html);
        $this->assertStringContainsString('ui-example-preview reference-scroll-preview', $html);
    }

    public function test_the_category_tree_renders_three_levels(): void
    {
        [$root] = $this->makeTree();

        $html = $this->structureHtml('treeList');

        $this->assertStringContainsString('data-control="liststructurewidget"', $html);
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
        $this->assertSame(1, $grandchild->nest_depth);
        $this->assertSame(0, $child->refresh()->getChildCount());
    }

    public function test_on_reorder_moves_a_category_after_a_sibling(): void
    {
        $first = Category::factory()->create(['name' => 'First', 'slug' => 'first']);
        $second = Category::factory()->create(['name' => 'Second', 'slug' => 'second']);

        $this->ajax('treeList::onReorder', [
            'record_id' => $first->id,
            'previous_id' => $second->id,
        ])->assertOk();

        $this->assertSame(['Second', 'First'], Category::pluck('name')->all());
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
