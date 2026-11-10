<?php

namespace Tests\Feature\Showcase;

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

    public function test_the_categories_show_as_a_tree_with_their_post_counts(): void
    {
        [$root] = $this->makeTree();
        Post::factory()->count(3)->for($root)->create();

        $html = $this->get('categories')->assertOk()->getContent();

        $this->assertStringContainsString('data-max-depth="3"', $html);
        $this->assertStringContainsString('data-tree-id="'.$root->id.'"', $html);
        $this->assertStringContainsString('href="'.url("categories/{$root->id}/edit").'"', $html);
        $this->assertMatchesRegularExpression('/list-cell-name-posts_count list-cell-type-number\s+"[^>]*>\s+3\s+</', $html);
        $this->assertMatchesRegularExpression('/<h1[^>]*>\s*Categories\s*<\/h1>/', $html);
    }

    public function test_dragging_a_category_under_another_nests_it(): void
    {
        [, $child, $grandchild] = $this->makeTree();
        $news = Category::factory()->create(['name' => 'News', 'slug' => 'news']);

        $this->ajax('list::onReorder', [
            'record_id' => $grandchild->id,
            'parent_id' => $news->id,
        ])->assertOk();

        $this->assertSame($news->id, $grandchild->refresh()->parent_id);
        $this->assertSame(0, $child->refresh()->getChildCount());
    }

    public function test_the_create_form_offers_the_tree_as_parents(): void
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

    public function test_on_store_creates_a_child_category_and_returns_to_the_tree(): void
    {
        [$root] = $this->makeTree();

        $response = $this->ajax('onStore', [
            'name' => 'Lists',
            'slug' => 'lists',
            'parent' => $root->id,
            'description' => 'All about lists.',
        ], 'categories/create')->assertOk()->assertSee('Category created.');

        $category = Category::where('slug', 'lists')->firstOrFail();
        $this->assertSame(route('categories.index'), $response->json('__ajax.redirect'));
        $this->assertSame($root->id, $category->parent_id);
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
        ], "categories/{$child->id}/edit")->assertOk()->assertSee('Category updated.');

        $child->refresh();
        $this->assertNull($child->parent_id);
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

        $response = $this->ajax('onDestroy', [], "categories/{$root->id}/edit")->assertOk();

        $this->assertSame(route('categories.index'), $response->json('__ajax.redirect'));
        $this->assertSame([$other->id], Category::pluck('id')->all());
    }

    public function test_on_destroy_refuses_while_a_subcategory_holds_posts(): void
    {
        [$root, , $grandchild] = $this->makeTree();
        Post::factory()->for($grandchild)->create();

        $this->ajax('onDestroy', [], "categories/{$root->id}/edit")->assertStatus(422);

        $this->assertSame(3, Category::count());
    }

    public function test_the_tree_has_checkboxes_and_a_delete_button_for_the_checked_rows(): void
    {
        $this->makeTree();

        $html = $this->get('categories')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/class="control-list[^"]*list-checkboxes"\s+data-control="listwidget liststructurewidget"/', $html);
        $this->assertMatchesRegularExpression('/<button[^>]*class="btn-solid theme-danger"[^>]*data-request="onDelete"[^>]*data-request-confirm="Delete the checked categories and the categories below them\?"[^>]*data-list-checked-trigger/', $html);
    }

    public function test_on_delete_deletes_the_checked_categories_with_the_categories_below_them(): void
    {
        [$root, $child] = $this->makeTree();
        $news = Category::factory()->create(['name' => 'News', 'slug' => 'news']);

        // The child is checked as well as its parent, and goes with it
        $response = $this->ajax('onDelete', ['checked' => [$child->id, $root->id]])->assertOk();

        $this->assertSame([$news->id], Category::pluck('id')->all());

        $ops = collect($response->json('__ajax.ops'));
        $this->assertSame('success', $ops->firstWhere('op', 'flash')['level']);
        $this->assertSame('2 categories deleted.', $ops->firstWhere('op', 'flash')['text']);
        $this->assertTrue($ops->contains(fn (array $op) => str_contains($op['html'] ?? '', 'data-tree-id="'.$news->id.'"')), 'The tree is not refreshed.');
    }

    public function test_on_delete_keeps_a_checked_branch_that_holds_posts(): void
    {
        [$root, , $grandchild] = $this->makeTree();
        Post::factory()->for($grandchild)->create();
        $news = Category::factory()->create(['name' => 'News', 'slug' => 'news']);

        $response = $this->ajax('onDelete', ['checked' => [$root->id, $news->id]])->assertOk();

        $flash = collect($response->json('__ajax.ops'))->firstWhere('op', 'flash');
        $this->assertSame('warning', $flash['level']);
        $this->assertSame('1 category deleted. Guides holds posts, so it stays.', $flash['text']);
        $this->assertSame(3, Category::count());
        $this->assertNull(Category::find($news->id));
    }

    public function test_on_delete_refuses_when_every_checked_branch_holds_posts(): void
    {
        [$root] = $this->makeTree();
        $news = Category::factory()->create(['name' => 'News', 'slug' => 'news']);
        Post::factory()->for($root)->create();
        Post::factory()->for($news)->create();

        $response = $this->ajax('onDelete', ['checked' => [$news->id, $root->id]])->assertOk();

        $flash = collect($response->json('__ajax.ops'))->firstWhere('op', 'flash');
        $this->assertSame('error', $flash['level']);
        $this->assertSame('Guides and News hold posts, so they stay. Move or delete the posts first.', $flash['text']);
        $this->assertSame(4, Category::count());
    }
}
