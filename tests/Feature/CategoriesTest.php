<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class CategoriesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ajax posts an AJAX request for the given handler to a categories page.
     *
     * @param  array<string, mixed>  $data
     */
    protected function ajax(string $handler, array $data = [], string $url = 'categories'): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post($url, $data);
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

    public function test_the_category_tree_renders_three_levels(): void
    {
        [$root, $child, $grandchild] = $this->makeTree();

        $this->get('categories')
            ->assertOk()
            ->assertSee('data-control="liststructurewidget"', false)
            ->assertSee('data-use-tree="1"', false)
            ->assertSee('data-max-depth="3"', false)
            ->assertSee('data-drag-row=""', false)
            ->assertSee('data-tree-id="'.$root->id.'"', false)
            ->assertSee('data-tree-level="2"', false)
            ->assertSee('tree-expand-collapse is-expanded', false)
            ->assertSeeInOrder(['Guides', 'Forms', 'Validation Rules']);
    }

    public function test_the_tree_counts_the_posts_of_each_category(): void
    {
        [$root] = $this->makeTree();
        Post::factory()->count(3)->for($root)->create();

        $html = $this->get('categories')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/list-cell-name-posts_count list-cell-type-number\s+"[^>]*>\s+3\s+</', $html);
    }

    public function test_on_reorder_nests_a_category_under_another(): void
    {
        [$root, $child, $grandchild] = $this->makeTree();
        $other = Category::factory()->create(['name' => 'News', 'slug' => 'news']);

        $this->ajax('list::onReorder', [
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

        $this->ajax('list::onReorder', [
            'record_id' => $first->id,
            'previous_id' => $second->id,
        ])->assertOk();

        $this->assertSame(['Second', 'First'], Category::pluck('name')->all());
    }

    public function test_on_toggle_tree_node_collapses_a_branch(): void
    {
        [$root] = $this->makeTree();

        $html = $this->ajax('list::onToggleTreeNode', [
            'node_id' => $root->id,
            'status' => 1,
        ])->assertOk()->json('__ajax.ops.0.html');

        $this->assertStringContainsString('data-tree-id="'.$root->id.'"', $html);
        $this->assertStringContainsString('data-tree-expanded="false"', $html);
        $this->assertStringNotContainsString('Validation Rules', $html);
    }

    public function test_sorting_a_column_shows_a_flat_list(): void
    {
        $this->makeTree();

        $html = $this->ajax('list::onSort', ['sortColumn' => 'name'])->assertOk()->json('__ajax.ops.0.html');

        $this->assertStringContainsString('data-use-tree=""', $html);
        $this->assertStringContainsString('Validation Rules', $html);
    }

    public function test_the_create_form_renders_the_tree_parent_options(): void
    {
        $this->makeTree();

        $this->get('categories/create')
            ->assertOk()
            ->assertSee('New Category')
            ->assertSee('No parent, show at the top level')
            ->assertSee('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Validation Rules', false)
            ->assertSee('data-input-preset="[name=&quot;name&quot;]"', false);
    }

    public function test_the_edit_form_leaves_out_the_category_being_edited(): void
    {
        [$root, $child] = $this->makeTree();

        $this->get("categories/{$child->id}/edit")
            ->assertOk()
            ->assertSee('Edit Category')
            ->assertSee('value="'.$root->id.'"', false)
            ->assertDontSee('<option value="'.$child->id.'"', false);
    }

    public function test_on_store_creates_a_child_category(): void
    {
        [$root] = $this->makeTree();

        $this->ajax('onStore', [
            'name' => 'Lists',
            'slug' => 'lists',
            'parent' => $root->id,
            'description' => 'All about lists.',
        ], 'categories/create')->assertOk();

        $category = Category::where('slug', 'lists')->firstOrFail();
        $this->assertSame($root->id, $category->parent_id);
        $this->assertSame(1, $category->nest_depth);
    }

    public function test_on_store_validates_a_unique_slug(): void
    {
        $this->makeTree();

        $this->ajax('onStore', [
            'name' => 'Guides Again',
            'slug' => 'guides',
        ], 'categories/create')->assertStatus(422);

        $this->assertSame(1, Category::where('slug', 'guides')->count());
    }

    public function test_on_update_moves_a_category_to_the_top_level(): void
    {
        [, $child] = $this->makeTree();

        $this->ajax('onUpdate', [
            'name' => 'Forms',
            'slug' => 'forms',
            'parent' => '',
        ], "categories/{$child->id}/edit")->assertOk();

        $child->refresh();
        $this->assertNull($child->parent_id);
        $this->assertSame(0, $child->nest_depth);
        $this->assertSame(1, $child->getChildCount());
    }

    public function test_on_update_refuses_to_nest_a_category_below_its_subcategory(): void
    {
        [$root, , $grandchild] = $this->makeTree();

        $this->ajax('onUpdate', [
            'name' => 'Guides',
            'slug' => 'guides',
            'parent' => $grandchild->id,
        ], "categories/{$root->id}/edit")->assertStatus(422);

        $this->assertNull($root->refresh()->parent_id);
    }

    public function test_on_destroy_deletes_a_category_with_its_subcategories(): void
    {
        [$root] = $this->makeTree();
        $other = Category::factory()->create(['name' => 'News', 'slug' => 'news']);

        $this->ajax('onDestroy', [], "categories/{$root->id}/edit")->assertOk();

        $this->assertSame([$other->id], Category::pluck('id')->all());
    }

    public function test_on_destroy_refuses_while_a_subcategory_holds_posts(): void
    {
        [$root, , $grandchild] = $this->makeTree();
        Post::factory()->for($grandchild)->create();

        $this->ajax('onDestroy', [], "categories/{$root->id}/edit")->assertStatus(422);

        $this->assertSame(3, Category::count());
    }
}
