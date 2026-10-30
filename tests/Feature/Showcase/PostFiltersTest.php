<?php

namespace Tests\Feature\Showcase;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PostFiltersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ajax posts an AJAX request for the given handler to the posts list.
     *
     * @param  array<string, mixed>  $data
     */
    protected function ajax(string $handler, array $data = []): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post('posts', $data);
    }

    public function test_archived_posts_are_hidden_until_the_filter_is_cleared(): void
    {
        Post::factory()->create(['title' => 'Current Article', 'status' => 'approved']);
        Post::factory()->create(['title' => 'Retired Article', 'status' => 'archived']);

        $this->get('posts')
            ->assertOk()
            ->assertSee('Current Article')
            ->assertDontSee('Retired Article');
    }

    public function test_clearing_hide_archived_shows_the_archived_posts(): void
    {
        Post::factory()->create(['title' => 'Retired Article', 'status' => 'archived']);

        $this->ajax('filter::onFilterUpdate', ['scopeName' => 'hide_archived', 'value' => '0'])
            ->assertOk()
            ->assertSee('Retired Article');
    }

    public function test_the_published_switch_shows_only_live_posts(): void
    {
        Post::factory()->published()->create(['title' => 'Live Article']);
        Post::factory()->create(['title' => 'Hidden Article']);

        $this->ajax('filter::onFilterUpdate', ['scopeName' => 'published', 'value' => '2'])
            ->assertOk()
            ->assertSee('Live Article')
            ->assertDontSee('Hidden Article');
    }

    public function test_the_author_filter_shows_the_posts_of_the_picked_author(): void
    {
        $ada = User::factory()->create(['name' => 'Ada Writer']);
        Post::factory()->for($ada, 'author')->create(['title' => 'Ada Article']);
        Post::factory()->for(User::factory()->create(), 'author')->create(['title' => 'Other Article']);
        Post::factory()->create(['title' => 'Anonymous Article']);

        $this->ajax('filter::onFilterUpdate', [
            'scopeName' => 'author',
            'Filter' => ['value' => json_encode([$ada->id])],
        ])
            ->assertOk()
            ->assertSee('Ada Article')
            ->assertDontSee('Other Article')
            ->assertDontSee('Anonymous Article');
    }

    public function test_the_author_filter_offers_only_users_who_wrote_posts(): void
    {
        $ada = User::factory()->create(['name' => 'Ada Writer']);
        User::factory()->create(['name' => 'Silent Reader']);
        Post::factory()->for($ada, 'author')->create();

        $names = collect($this->ajax('filterAuthor::onGetGroupOptions')->assertOk()->json('options.available'))
            ->pluck('name')
            ->all();

        $this->assertSame(['Ada Writer'], $names);
    }

    public function test_the_category_filter_shows_the_posts_of_the_picked_categories(): void
    {
        $guides = Category::factory()->create(['name' => 'Guides', 'slug' => 'guides']);
        Post::factory()->for($guides)->create(['title' => 'Guide Article']);
        Post::factory()->create(['title' => 'Other Article']);

        $this->ajax('filter::onFilterUpdate', [
            'scopeName' => 'category',
            'Filter' => ['value' => json_encode([$guides->id])],
        ])
            ->assertOk()
            ->assertSee('Guide Article')
            ->assertDontSee('Other Article');
    }

    public function test_the_untagged_option_finds_posts_without_tags(): void
    {
        Post::factory()->create(['title' => 'Untagged Article']);
        Post::factory()->create(['title' => 'Tagged Article'])->tags()->attach(Tag::factory()->create());

        $this->ajax('filter::onFilterUpdate', [
            'scopeName' => 'tags',
            'Filter' => ['value' => json_encode([''])],
        ])
            ->assertOk()
            ->assertSee('Untagged Article')
            ->assertDontSee('Tagged Article');
    }

    public function test_the_published_date_range_finds_posts_published_between_two_days(): void
    {
        Post::factory()->create(['title' => 'January Article', 'published' => true, 'published_at' => '2026-01-15 09:00:00']);
        Post::factory()->create(['title' => 'March Article', 'published' => true, 'published_at' => '2026-03-15 09:00:00']);

        $this->ajax('filter::onFilterUpdate', [
            'scopeName' => 'published_at',
            'Filter' => [
                'condition' => 'between',
                'after' => '2026-01-01',
                'afterRaw' => '2026-01-01',
                'before' => '2026-01-31',
                'beforeRaw' => '2026-01-31',
            ],
        ])
            ->assertOk()
            ->assertSee('January Article')
            ->assertDontSee('March Article');
    }
}
