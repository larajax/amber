<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PostFilterTest extends TestCase
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

    public function test_archived_posts_are_hidden_by_the_default_checkbox_scope(): void
    {
        Post::factory()->create(['title' => 'Current Article', 'status' => 'approved']);
        Post::factory()->create(['title' => 'Retired Article', 'status' => 'archived']);

        $this->get('posts')
            ->assertOk()
            ->assertSee('Current Article')
            ->assertDontSee('Retired Article');
    }

    public function test_clearing_the_default_checkbox_shows_archived_posts(): void
    {
        Post::factory()->create(['title' => 'Retired Article', 'status' => 'archived']);

        $this->ajax('filter::onFilterUpdate', ['scopeName' => 'hide_archived', 'value' => '0'])
            ->assertOk()
            ->assertSee('Retired Article');
    }

    public function test_the_switch_scope_applies_its_second_condition(): void
    {
        Post::factory()->published()->create(['title' => 'Live Article']);
        Post::factory()->create(['title' => 'Hidden Article']);

        $this->ajax('filter::onFilterUpdate', ['scopeName' => 'published', 'value' => '2'])
            ->assertOk()
            ->assertSee('Live Article')
            ->assertDontSee('Hidden Article');
    }

    public function test_the_switch_scope_applies_its_first_condition(): void
    {
        Post::factory()->published()->create(['title' => 'Live Article']);
        Post::factory()->create(['title' => 'Hidden Article']);

        $this->ajax('filter::onFilterUpdate', ['scopeName' => 'published', 'value' => '1'])
            ->assertOk()
            ->assertSee('Hidden Article')
            ->assertDontSee('Live Article');
    }

    public function test_the_category_group_scope_filters_by_the_picked_categories(): void
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

    public function test_the_category_group_options_only_offer_categories_with_posts(): void
    {
        $used = Category::factory()->create(['name' => 'Used Category', 'slug' => 'used-category']);
        Category::factory()->create(['name' => 'Empty Category', 'slug' => 'empty-category']);
        Post::factory()->for($used)->create();

        $names = collect($this->ajax('filterCategory::onGetGroupOptions')->assertOk()->json('options.available'))
            ->pluck('name')
            ->all();

        $this->assertContains('Used Category', $names);
        $this->assertNotContains('Empty Category', $names);
    }

    public function test_group_options_named_like_a_language_file_keep_their_name(): void
    {
        $category = Category::factory()->create(['name' => 'validation', 'slug' => 'validation']);
        Post::factory()->for($category)->create();

        $available = $this->ajax('filterCategory::onGetGroupOptions')->assertOk()->json('options.available');

        $this->assertSame([['id' => $category->id, 'name' => 'validation']], $available);
    }

    public function test_the_tags_empty_option_matches_untagged_posts_and_keeps_the_scopes_before_it(): void
    {
        Post::factory()->published()->create(['title' => 'Untagged Live']);
        Post::factory()->create(['title' => 'Untagged Hidden']);
        $tagged = Post::factory()->published()->create(['title' => 'Tagged Live']);
        $tagged->tags()->attach(Tag::factory()->create());

        // The published switch comes before the tags scope, so the empty option must not widen it
        $this->withSession([
            'widget.app-PostsController-Filter' => json_encode([
                'scope-published' => ['value' => '2'],
            ]),
        ])->ajax('filter::onFilterUpdate', [
            'scopeName' => 'tags',
            'Filter' => ['value' => json_encode([''])],
        ])
            ->assertOk()
            ->assertSee('Untagged Live')
            ->assertDontSee('Untagged Hidden')
            ->assertDontSee('Tagged Live');
    }

    public function test_the_tags_empty_option_keeps_the_search_term(): void
    {
        Post::factory()->create(['title' => 'Untagged Banana']);
        Post::factory()->create(['title' => 'Untagged Cherry']);

        $this->withSession([
            'widget.app-PostsController-Search-toolbarSearch' => json_encode(['term' => 'banana']),
        ])->ajax('filter::onFilterUpdate', [
            'scopeName' => 'tags',
            'Filter' => ['value' => json_encode([''])],
        ])
            ->assertOk()
            ->assertSee('Untagged Banana')
            ->assertDontSee('Untagged Cherry');
    }

    public function test_the_daterange_scope_matches_posts_published_between_two_dates(): void
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

    public function test_the_numberrange_scope_matches_views_between_two_numbers(): void
    {
        Post::factory()->create(['title' => 'Quiet Article', 'views' => 10]);
        Post::factory()->create(['title' => 'Popular Article', 'views' => 500]);

        $this->ajax('filter::onFilterUpdate', [
            'scopeName' => 'views',
            'Filter' => ['condition' => 'between', 'min' => '100', 'max' => '1000'],
        ])
            ->assertOk()
            ->assertSee('Popular Article')
            ->assertDontSee('Quiet Article');
    }

    public function test_the_number_scope_runs_a_named_sql_condition(): void
    {
        $discussed = Post::factory()->create(['title' => 'Discussed Article']);
        Comment::factory()->count(3)->for($discussed)->create();
        Post::factory()->create(['title' => 'Silent Article']);

        $this->ajax('filter::onFilterUpdate', [
            'scopeName' => 'comment_count',
            'Filter' => ['condition' => 'greater', 'value' => '2'],
        ])
            ->assertOk()
            ->assertSee('Discussed Article')
            ->assertDontSee('Silent Article');
    }

    public function test_the_number_scope_only_offers_its_named_conditions(): void
    {
        $content = $this->ajax('filter::onFilterUpdate', ['preload' => 'true'])
            ->assertOk()
            ->json('popoverContent.comment_count');

        $this->assertStringContainsString('value="greater"', $content);
        $this->assertStringContainsString('value="lesser"', $content);
        $this->assertStringNotContainsString('value="between"', $content);
    }

    public function test_the_date_scope_runs_a_named_sql_condition(): void
    {
        Post::factory()->create(['title' => 'Due Soon Article', 'review_on' => '2026-11-02']);
        Post::factory()->create(['title' => 'Due Later Article', 'review_on' => '2027-02-01']);

        $this->ajax('filter::onFilterUpdate', [
            'scopeName' => 'review_on',
            'Filter' => ['condition' => 'before', 'value' => '2026-12-01', 'valueRaw' => '2026-12-01'],
        ])
            ->assertOk()
            ->assertSee('Due Soon Article')
            ->assertDontSee('Due Later Article');
    }
}
