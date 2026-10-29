<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\File;
use App\Models\Post;
use App\Models\Tag;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Widgets\Lists;
use Tests\TestCase;

class PostsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * tearDown removes the stored image files and clears the list preferences, which the widget caches statically for the whole process.
     */
    protected function tearDown(): void
    {
        File::all()->each->delete();

        Closure::bind(fn () => static::$preferenceCache = [], null, Lists::class)();

        parent::tearDown();
    }

    /**
     * ajax posts an AJAX request for the given handler to a posts page.
     *
     * @param  array<string, mixed>  $data
     */
    protected function ajax(string $handler, array $data = [], string $url = 'posts'): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post($url, $data);
    }

    public function test_the_posts_list_renders_each_column_type(): void
    {
        $category = Category::factory()->create(['name' => 'Guides', 'slug' => 'guides']);
        $post = Post::factory()->published()->for($category)->create([
            'title' => 'Hello Columns',
            'status' => 'approved',
            'featured_color' => '#ff0000',
        ]);
        $post->tags()->attach([
            Tag::factory()->create(['name' => 'alpha'])->id,
            Tag::factory()->create(['name' => 'beta'])->id,
            Tag::factory()->create(['name' => 'gamma'])->id,
            Tag::factory()->create(['name' => 'delta'])->id,
        ]);
        Comment::factory()->count(2)->for($post)->create();

        $this->get('posts')
            ->assertOk()
            ->assertSee('Hello Columns')
            // Relation select rendered as a linkage to the category
            ->assertSee('href="'.url("categories/{$category->id}/edit").'"', false)
            ->assertSee('Guides')
            // Partial column with a header tooltip, showing three tags and a counter
            ->assertSee('data-bs-title="Up to three tags show here, open the post to see all of them."', false)
            ->assertSee('alpha')
            ->assertSee('+1')
            // Selectable column with the status icon
            ->assertSee('ph ph-seal-check', false)
            ->assertSee('Approved')
            // Switch column with its option labels
            ->assertSee('list-switch is-true', false)
            ->assertSee('title="Published"', false)
            // Relation count and color picker columns
            ->assertSee('list-cell-type-number', false)
            ->assertSee('list-cell-type-timetense', false)
            ->assertSee('--background-color:#ff0000', false)
            // Invisible columns start hidden
            ->assertDontSee('list-cell-name-excerpt', false)
            ->assertDontSee('list-cell-name-review_on', false);
    }

    public function test_the_relation_count_column_counts_the_comments(): void
    {
        $post = Post::factory()->create();
        Comment::factory()->count(4)->for($post)->create();

        $html = $this->get('posts')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/list-cell-name-comments_count list-cell-type-number\s+">\s+4\s+</', $html);
    }

    public function test_the_image_column_renders_a_thumbnail_of_the_featured_image(): void
    {
        $post = Post::factory()->create();
        $post->featured_image()->create(['data' => UploadedFile::fake()->image('cover.png', 200, 120)]);

        $this->get('posts')
            ->assertOk()
            ->assertSee('list-image-thumb', false)
            ->assertSee('thumb_', false);
    }

    public function test_the_list_shows_the_no_records_message(): void
    {
        $this->get('posts')
            ->assertOk()
            ->assertSee('No posts match the search and filters.');
    }

    public function test_the_list_sorts_by_the_publish_date_newest_first(): void
    {
        Post::factory()->create(['title' => 'Older Article', 'published' => true, 'published_at' => '2026-01-01 10:00:00']);
        Post::factory()->create(['title' => 'Newer Article', 'published' => true, 'published_at' => '2026-06-01 10:00:00']);

        $this->get('posts')
            ->assertOk()
            ->assertSeeInOrder(['Newer Article', 'Older Article']);
    }

    public function test_the_list_setup_popup_offers_the_columns_and_page_sizes(): void
    {
        $content = $this->ajax('list::onLoadSetup')->assertOk()->json('result');

        $this->assertStringContainsString('value="excerpt"', $content);
        $this->assertStringContainsString('value="review_on"', $content);
        $this->assertStringContainsString('<option value="50" >', $content);
        $this->assertStringContainsString('<option value="10" selected="selected">', $content);
    }

    public function test_applying_the_list_setup_shows_an_invisible_column(): void
    {
        Post::factory()->create(['excerpt' => 'A summary that should now be shown in the list.']);

        $this->ajax('list::onApplySetup', [
            'visible_columns' => ['title', 'excerpt'],
            'column_order' => ['title', 'excerpt'],
            'records_per_page' => 20,
        ])
            ->assertOk()
            ->assertSee('list-cell-name-excerpt', false)
            ->assertDontSee('list-cell-name-status', false);
    }

    public function test_the_search_scope_matches_tag_and_category_names(): void
    {
        $tagged = Post::factory()->create(['title' => 'First Article']);
        $tagged->tags()->attach(Tag::factory()->create(['name' => 'zebra']));
        Post::factory()->for(Category::factory()->create(['name' => 'Zebra Crossing', 'slug' => 'zebra-crossing']))
            ->create(['title' => 'Second Article']);
        Post::factory()->create(['title' => 'Third Article']);

        $this->ajax('toolbarSearch::onSubmit', ['toolbarSearch' => ['term' => 'zebra']])
            ->assertOk()
            ->assertSee('First Article')
            ->assertSee('Second Article')
            ->assertDontSee('Third Article');
    }

    public function test_the_toolbar_renders_the_popup_button_hotkey_and_bulk_menu(): void
    {
        $this->get('posts')
            ->assertOk()
            ->assertSee('data-control="popup"', false)
            ->assertSee('data-handler="onLoadQuickPost"', false)
            ->assertSee('data-hotkey="shift+q"', false)
            ->assertSee('data-bs-toggle="menu"', false)
            ->assertSee('data-list-checked-trigger', false)
            ->assertSee('data-request="onBulkPublish"', false)
            ->assertSee('data-request="onBulkUnpublish"', false)
            ->assertSee('data-request="onBulkDelete"', false)
            ->assertSee('data-list-checked-request', false)
            ->assertSee('data-handler="list::onLoadSetup"', false);
    }

    public function test_on_load_quick_post_renders_the_popup_form(): void
    {
        Category::factory()->create(['name' => 'Guides', 'slug' => 'guides']);

        $content = $this->ajax('onLoadQuickPost')->assertOk()->json('result');

        $this->assertStringContainsString('Quick Post', $content);
        $this->assertStringContainsString('data-request="onCreateQuickPost"', $content);
        $this->assertStringContainsString('name="title"', $content);
        $this->assertStringContainsString('data-input-preset', $content);
        $this->assertStringContainsString('Guides', $content);
    }

    public function test_on_create_quick_post_creates_a_post_and_refreshes_the_list(): void
    {
        $category = Category::factory()->create();

        $this->ajax('onCreateQuickPost', [
            'title' => 'Quick Thought',
            'slug' => 'quick-thought',
            'category' => $category->id,
            'excerpt' => 'Written in a popup.',
            'published' => '1',
        ])
            ->assertOk()
            ->assertSee('Quick Thought');

        $post = Post::where('slug', 'quick-thought')->firstOrFail();
        $this->assertSame($category->id, $post->category_id);
        $this->assertTrue($post->published);
        $this->assertNotNull($post->published_at);
        $this->assertSame('draft', $post->status);
    }

    public function test_on_create_quick_post_validates_the_required_fields(): void
    {
        $this->ajax('onCreateQuickPost', ['title' => '', 'slug' => '', 'category' => ''])
            ->assertStatus(422);

        $this->assertSame(0, Post::count());
    }

    public function test_on_bulk_publish_publishes_the_checked_posts(): void
    {
        $posts = Post::factory()->count(3)->create();

        $this->ajax('onBulkPublish', ['checked' => [$posts[0]->id, $posts[2]->id]])
            ->assertOk()
            ->assertSee('2 posts published.');

        $this->assertTrue($posts[0]->refresh()->published);
        $this->assertNotNull($posts[0]->published_at);
        $this->assertFalse($posts[1]->refresh()->published);
        $this->assertTrue($posts[2]->refresh()->published);
    }

    public function test_on_bulk_unpublish_keeps_the_publish_date(): void
    {
        $post = Post::factory()->published()->create();
        $publishedAt = $post->published_at;

        $this->ajax('onBulkUnpublish', ['checked' => [$post->id]])
            ->assertOk()
            ->assertSee('1 post unpublished.');

        $post->refresh();
        $this->assertFalse($post->published);
        $this->assertEquals($publishedAt, $post->published_at);
    }

    public function test_on_bulk_delete_deletes_the_checked_posts_with_their_comments(): void
    {
        $posts = Post::factory()->count(2)->create();
        Comment::factory()->for($posts[0])->create();

        $this->ajax('onBulkDelete', ['checked' => [$posts[0]->id]])
            ->assertOk()
            ->assertSee('1 post deleted.');

        $this->assertSame([$posts[1]->id], Post::pluck('id')->all());
        $this->assertSame(0, Comment::count());
    }

    public function test_the_create_form_renders_with_the_slug_preset(): void
    {
        Category::factory()->create(['name' => 'Guides', 'slug' => 'guides']);

        $this->get('posts/create')
            ->assertOk()
            ->assertSee('New Post')
            ->assertSee('data-input-preset="[name=&quot;title&quot;]"', false)
            ->assertSee('data-hotkey="ctrl+s,cmd+s"', false);
    }

    public function test_the_edit_form_renders_the_tabs_widgets_and_trigger(): void
    {
        $category = Category::factory()->create(['name' => 'Guides', 'slug' => 'guides']);
        $post = Post::factory()->published()->for($category)->create([
            'title' => 'Editable Article',
            'slug' => 'editable-article',
            'keywords' => ['testing'],
        ]);

        $this->get("posts/{$post->id}/edit")
            ->assertOk()
            ->assertSee('Editable Article')
            // Read-only slug in the update context
            ->assertSee('value="editable-article"', false)
            ->assertSee('readonly', false)
            // Secondary tabs with the code and markdown editors
            ->assertSee('secondary-tabs', false)
            ->assertSee('data-control="codeeditor"', false)
            ->assertSee('Editor Notes')
            // Tag lists, date pickers, file upload and color picker
            ->assertSee('select-hide-selected', false)
            ->assertSee('data-control="datepicker"', false)
            ->assertSee('data-mode="time"', false)
            ->assertSee('data-control="fileupload"', false)
            ->assertSee('data-control="colorpicker"', false)
            // The publish date shows only while the switch is on
            ->assertSee('data-trigger="[name=&quot;published&quot;]"', false)
            ->assertSee('data-trigger-condition="checked"', false)
            // The permalink depends on the category and slug
            ->assertSee('/blog/guides/editable-article')
            ->assertSee('data-field-depends', false);
    }

    public function test_the_category_dropdown_follows_the_tree(): void
    {
        $parent = Category::factory()->create(['name' => 'Guides', 'slug' => 'guides']);
        Category::factory()->childOf($parent)->create(['name' => 'Forms', 'slug' => 'forms']);

        $this->get('posts/create')
            ->assertOk()
            ->assertSee('Guides')
            ->assertSee('&nbsp;&nbsp;&nbsp;Forms', false);
    }

    public function test_values_named_like_a_language_file_render_as_given(): void
    {
        // A translated value named like a language file would otherwise be the whole file array
        $category = Category::factory()->create(['name' => 'validation', 'slug' => 'validation']);
        $post = Post::factory()->for($category)->create();

        $this->get('posts')
            ->assertOk()
            ->assertSee('>validation</a>', false);

        $this->get("posts/{$post->id}/edit")
            ->assertOk()
            ->assertSee('>validation</option>', false);
    }

    public function test_the_permalink_refreshes_when_the_category_changes(): void
    {
        $guides = Category::factory()->create(['name' => 'Guides', 'slug' => 'guides']);
        $news = Category::factory()->create(['name' => 'News', 'slug' => 'news']);
        $post = Post::factory()->for($guides)->create(['slug' => 'moving-article']);

        $html = $this->ajax('form::onRefresh', [
            'category' => $news->id,
            'slug' => 'moving-article',
            'fields' => ['_permalink'],
        ], "posts/{$post->id}/edit")
            ->assertOk()
            ->json('__ajax.ops.0.html');

        $this->assertStringContainsString('value="/blog/news/moving-article"', $html);
    }

    public function test_on_store_creates_a_post_with_tags_and_keywords(): void
    {
        $category = Category::factory()->create();
        $existing = Tag::factory()->create(['name' => 'laravel']);

        $this->ajax('onStore', [
            'title' => 'Fresh Article',
            'slug' => 'fresh-article',
            'category' => $category->id,
            'status' => 'review',
            'tags' => ['laravel', 'brand-new'],
            'keywords' => ['testing', 'tutorial'],
            'published' => '0',
            'review_on' => '2026-12-01',
            'share_time' => '09:30',
            'views' => '',
        ], 'posts/create')->assertOk();

        $post = Post::where('slug', 'fresh-article')->firstOrFail();
        $this->assertSame('review', $post->status);
        $this->assertSame(['testing', 'tutorial'], $post->keywords);
        $this->assertSame(0, $post->views);
        $this->assertSame('2026-12-01', $post->review_on->toDateString());
        $this->assertEqualsCanonicalizing(['laravel', 'brand-new'], $post->tags->pluck('name')->all());
        $this->assertTrue($post->tags->contains($existing));
    }

    public function test_on_store_redirects_to_the_new_post(): void
    {
        $category = Category::factory()->create();

        $resp = $this->ajax('onStore', [
            'title' => 'Redirected Article',
            'slug' => 'redirected-article',
            'category' => $category->id,
        ], 'posts/create')->assertOk();

        $post = Post::where('slug', 'redirected-article')->firstOrFail();
        $this->assertStringContainsString("posts/{$post->id}/edit", $resp->json('__ajax.redirect'));
    }

    public function test_on_store_validates_the_slug_format(): void
    {
        $category = Category::factory()->create();

        $this->ajax('onStore', [
            'title' => 'Bad Slug',
            'slug' => 'Not A Slug',
            'category' => $category->id,
        ], 'posts/create')->assertStatus(422);

        $this->assertSame(0, Post::count());
    }

    public function test_on_update_saves_the_category_and_clears_the_tags(): void
    {
        $from = Category::factory()->create();
        $to = Category::factory()->create();
        $post = Post::factory()->for($from)->create(['title' => 'Original Title']);
        $post->tags()->attach(Tag::factory()->create());

        $this->ajax('onUpdate', [
            'title' => 'Changed Title',
            'slug' => $post->slug,
            'category' => $to->id,
            'tags' => '',
            'published' => '1',
        ], "posts/{$post->id}/edit")->assertOk();

        $post->refresh();
        $this->assertSame('Changed Title', $post->title);
        $this->assertSame($to->id, $post->category_id);
        $this->assertSame(0, $post->tags()->count());
        $this->assertTrue($post->published);
        $this->assertNotNull($post->published_at);
    }

    public function test_on_destroy_deletes_the_post(): void
    {
        $post = Post::factory()->create();
        Comment::factory()->for($post)->create();

        $this->ajax('onDestroy', [], "posts/{$post->id}/edit")->assertOk();

        $this->assertSame(0, Post::count());
        $this->assertSame(0, Comment::count());
    }
}
