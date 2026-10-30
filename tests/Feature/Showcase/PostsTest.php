<?php

namespace Tests\Feature\Showcase;

use App\Models\Category;
use App\Models\Comment;
use App\Models\File;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PostsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * tearDown removes the stored image files, which the rollback alone would leave behind.
     */
    protected function tearDown(): void
    {
        File::all()->each->delete();

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

    public function test_the_list_shows_each_post_with_its_author_category_tags_and_status(): void
    {
        $author = User::factory()->create(['name' => 'Ada Writer']);
        $category = Category::factory()->create(['name' => 'Guides', 'slug' => 'guides']);
        $post = Post::factory()->published()->for($category)->for($author, 'author')->create([
            'title' => 'Hello Blog',
            'status' => 'approved',
        ]);
        $post->tags()->attach(Tag::factory()->count(4)->sequence(
            ['name' => 'alpha'], ['name' => 'beta'], ['name' => 'gamma'], ['name' => 'delta']
        )->create());
        Comment::factory()->count(2)->for($post)->create();

        $html = $this->get('posts')->assertOk()->getContent();

        $this->assertStringContainsString('Hello Blog', $html);
        $this->assertStringContainsString('href="'.route('authors.edit', $author).'" >Ada Writer</a>', $html);
        $this->assertStringContainsString('href="'.route('categories.edit', $category).'" >Guides</a>', $html);
        $this->assertStringContainsString('href="'.route('posts.edit', $post).'"', $html);

        // The tags partial shows three tags and counts the rest
        $this->assertStringContainsString('alpha', $html);
        $this->assertMatchesRegularExpression('/badge-outline theme-secondary">\s*\+1\s*</', $html);

        $this->assertStringContainsString('Approved', $html);
        $this->assertMatchesRegularExpression('/list-cell-name-comments_count list-cell-type-number\s+">\s+2\s+</', $html);
    }

    public function test_the_list_shows_the_newest_posts_first(): void
    {
        Post::factory()->create(['title' => 'Older Article', 'published' => true, 'published_at' => '2026-01-01 10:00:00']);
        Post::factory()->create(['title' => 'Newer Article', 'published' => true, 'published_at' => '2026-06-01 10:00:00']);

        $this->get('posts')
            ->assertOk()
            ->assertSeeInOrder(['Newer Article', 'Older Article']);
    }

    public function test_the_list_says_when_no_posts_match(): void
    {
        $this->get('posts')
            ->assertOk()
            ->assertSee('No posts match the search and filters.');
    }

    public function test_the_toolbar_offers_a_new_post_a_quick_post_and_bulk_actions(): void
    {
        $this->get('posts')
            ->assertOk()
            ->assertSee('href="'.url('posts/create').'"', false)
            ->assertSee('data-handler="onLoadQuickPost"', false)
            ->assertSee('data-hotkey="shift+q"', false)
            ->assertSee('data-request="onBulkPublish"', false)
            ->assertSee('data-request="onBulkUnpublish"', false)
            ->assertSee('data-request="onBulkDelete"', false)
            ->assertSee('data-handler="list::onLoadSetup"', false);
    }

    public function test_searching_matches_the_author_name(): void
    {
        Post::factory()->for(User::factory()->create(['name' => 'Ada Writer']), 'author')->create(['title' => 'First Article']);
        Post::factory()->for(User::factory()->create(['name' => 'Bo Other']), 'author')->create(['title' => 'Second Article']);

        $this->ajax('toolbarSearch::onSubmit', ['toolbarSearch' => ['term' => 'ada']])
            ->assertOk()
            ->assertSee('First Article')
            ->assertDontSee('Second Article');
    }

    public function test_on_load_quick_post_renders_the_popup_form(): void
    {
        Category::factory()->create(['name' => 'Guides', 'slug' => 'guides']);

        $content = $this->ajax('onLoadQuickPost')->assertOk()->json('result');

        $this->assertStringContainsString('Quick Post', $content);
        $this->assertStringContainsString('data-request="onCreateQuickPost"', $content);
        $this->assertStringContainsString('name="title"', $content);
        $this->assertStringContainsString('Guides', $content);
    }

    public function test_on_create_quick_post_creates_a_draft_and_refreshes_the_list(): void
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
            ->assertSee('Post created.')
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

    public function test_the_create_form_offers_the_authors_and_posts_its_session_key(): void
    {
        User::factory()->create(['name' => 'Zed Writer']);
        User::factory()->create(['name' => 'Ada Writer']);

        $html = $this->get('posts/create')->assertOk()->getContent();

        $this->assertStringContainsString('New Post', $html);
        $this->assertStringContainsString('data-input-preset="[name=&quot;title&quot;]"', $html);
        $this->assertMatchesRegularExpression('/<select[^>]*name="author".*?value=""\s*>No author<\/option>.*?>Ada Writer<\/option>.*?>Zed Writer<\/option>/s', $html);
        $this->assertMatchesRegularExpression('/<input type="hidden" name="_session_key" value="[^"]{40}"/', $html);
        $this->assertStringContainsString('data-hotkey="ctrl+s,cmd+s"', $html);
    }

    public function test_the_edit_form_shows_the_post_with_its_author_and_editors(): void
    {
        $author = User::factory()->create(['name' => 'Ada Writer']);
        $category = Category::factory()->create(['name' => 'Guides', 'slug' => 'guides']);
        $post = Post::factory()->published()->for($category)->for($author, 'author')->create([
            'title' => 'Editable Article',
            'slug' => 'editable-article',
        ]);

        $html = $this->get("posts/{$post->id}/edit")->assertOk()->getContent();

        $this->assertStringContainsString('Editable Article', $html);
        $this->assertMatchesRegularExpression('/name="slug"[^>]*value="editable-article"[^>]*readonly/s', $html);
        $this->assertMatchesRegularExpression('/selected="selected"\s+value="'.$author->id.'"\s*>Ada Writer<\/option>/', $html);
        $this->assertStringContainsString('/blog/guides/editable-article', $html);
        $this->assertStringContainsString('data-trigger="[name=&quot;published&quot;]"', $html);
        $this->assertStringContainsString('data-control="fileupload"', $html);
        $this->assertStringContainsString('data-control="richeditor"', $html);
        $this->assertStringContainsString('Editor Notes', $html);
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

    public function test_on_store_creates_a_post_by_its_author_and_opens_it(): void
    {
        $author = User::factory()->create();
        $category = Category::factory()->create();
        $existing = Tag::factory()->create(['name' => 'laravel']);

        $response = $this->ajax('onStore', [
            'title' => 'Fresh Article',
            'slug' => 'fresh-article',
            'category' => $category->id,
            'author' => $author->id,
            'status' => 'review',
            'tags' => ['laravel', 'brand-new'],
            'published' => '0',
        ], 'posts/create')->assertOk()->assertSee('Post created.');

        $post = Post::where('slug', 'fresh-article')->firstOrFail();
        $this->assertSame(route('posts.edit', $post), $response->json('__ajax.redirect'));
        $this->assertSame($author->id, $post->author_id);
        $this->assertSame('review', $post->status);
        $this->assertEqualsCanonicalizing(['laravel', 'brand-new'], $post->tags->pluck('name')->all());
        $this->assertTrue($post->tags->contains($existing));
    }

    public function test_on_store_attaches_the_image_uploaded_against_the_session_key(): void
    {
        $category = Category::factory()->create();

        // The upload field holds the image against the form's session key until the post is saved
        $image = new File;
        $image->data = UploadedFile::fake()->image('cover.png', 40, 40);
        $image->save();
        (new Post)->featured_image()->add($image, 'session-1');

        $this->ajax('onStore', [
            'title' => 'Illustrated Article',
            'slug' => 'illustrated-article',
            'category' => $category->id,
            '_session_key' => 'session-1',
        ], 'posts/create')->assertOk();

        $post = Post::where('slug', 'illustrated-article')->firstOrFail();
        $this->assertSame('cover.png', $post->featured_image->file_name);
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

    public function test_on_update_moves_the_post_removes_its_author_and_clears_the_tags(): void
    {
        $from = Category::factory()->create();
        $to = Category::factory()->create();
        $post = Post::factory()->for($from)->for(User::factory()->create(), 'author')->create(['title' => 'Original Title']);
        $post->tags()->attach(Tag::factory()->create());

        $this->ajax('onUpdate', [
            'title' => 'Changed Title',
            'slug' => $post->slug,
            'category' => $to->id,
            'author' => '',
            'tags' => '',
            'published' => '1',
        ], "posts/{$post->id}/edit")->assertOk()->assertSee('Post updated.');

        $post->refresh();
        $this->assertSame('Changed Title', $post->title);
        $this->assertSame($to->id, $post->category_id);
        $this->assertNull($post->author_id);
        $this->assertSame(0, $post->tags()->count());
        $this->assertTrue($post->published);
        $this->assertNotNull($post->published_at);
    }

    public function test_on_destroy_deletes_the_post_with_its_comments_and_returns_to_the_list(): void
    {
        $post = Post::factory()->create();
        Comment::factory()->for($post)->create();

        $response = $this->ajax('onDestroy', [], "posts/{$post->id}/edit")->assertOk();

        $this->assertSame(route('posts.index'), $response->json('__ajax.redirect'));
        $this->assertSame(0, Post::count());
        $this->assertSame(0, Comment::count());
    }
}
