<?php

namespace Tests\Feature\Showcase;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::parse('2026-11-18 12:00:00'));
    }

    /**
     * makeBlog creates two sections with posts in every status, three of them published across the last two months.
     *
     * @return array<string, Post>
     */
    protected function makeBlog(): array
    {
        $guides = Category::factory()->create(['name' => 'Guides', 'slug' => 'guides']);
        $forms = Category::factory()->childOf($guides)->create(['name' => 'Forms', 'slug' => 'forms']);
        $news = Category::factory()->create(['name' => 'News', 'slug' => 'news']);

        $posts = [
            'recent' => Post::factory()->for($forms)->create(['title' => 'Recent Post', 'status' => 'approved', 'published' => true, 'published_at' => '2026-11-17 09:00:00', 'views' => 900]),
            'earlier' => Post::factory()->for($news)->create(['title' => 'Earlier Post', 'status' => 'approved', 'published' => true, 'published_at' => '2026-11-03 09:00:00', 'views' => 300]),
            'older' => Post::factory()->for($guides)->create(['title' => 'Older Post', 'status' => 'approved', 'published' => true, 'published_at' => '2026-10-01 09:00:00', 'views' => 1200]),
            'draft' => Post::factory()->for($news)->create(['title' => 'Draft Post', 'status' => 'draft', 'views' => 5000]),
            'review' => Post::factory()->for($guides)->create(['title' => 'Review Post', 'status' => 'review', 'review_on' => '2026-11-25']),
            'archived' => Post::factory()->for($news)->create(['title' => 'Archived Post', 'status' => 'archived']),
        ];

        Comment::factory()->count(2)->for($posts['recent'])->create();
        Comment::factory()->for($posts['earlier'])->create()->delete();

        return $posts;
    }

    /**
     * scoreboardItem returns the markup of the scoreboard item with the given label.
     */
    protected function scoreboardItem(string $html, string $label): string
    {
        foreach (explode('<div class="scoreboard-item', $html) as $item) {
            if (preg_match('/scoreboard-label">\s*'.preg_quote($label, '/').'\b/', $item)) {
                return $item;
            }
        }

        $this->fail("The scoreboard has no {$label} item.");
    }

    public function test_the_scoreboard_counts_the_posts_views_comments_and_monthly_goal(): void
    {
        $this->makeBlog();

        $html = $this->get(route('dashboard.index'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<h1[^>]*>\s*Dashboard\s*<\/h1>/', $html);

        // The pie leaves out archived posts
        $this->assertMatchesRegularExpression('/Draft\s*<span>1<\/span>/', $html);
        $this->assertMatchesRegularExpression('/In Review\s*<span>1<\/span>/', $html);
        $this->assertMatchesRegularExpression('/Approved\s*<span>3<\/span>/', $html);
        $this->assertDoesNotMatchRegularExpression('/Archived\s*<span>/', $html);

        // Two published in the last 30 days against one in the 30 before
        $published = $this->scoreboardItem($html, 'Published');
        $this->assertMatchesRegularExpression('/<p>\s*3\s*<span class="scoreboard-trend is-positive">/', $published);
        $this->assertMatchesRegularExpression('/100%\s*<\/span>/', $published);
        $this->assertStringContainsString('2 in the last 30 days', $published);
        $this->assertStringContainsString('href="'.route('posts.index').'"', $published);

        // Unpublished posts leave their views out
        $views = $this->scoreboardItem($html, 'Views');
        $this->assertMatchesRegularExpression('/<p>\s*2,400\s*<\/p>/', $views);
        $this->assertStringContainsString('800 a post on average', $views);
        $this->assertStringContainsString('scoreboard-sparkline', $views);

        $comments = $this->scoreboardItem($html, 'Comments');
        $this->assertMatchesRegularExpression('/<p>\s*2\s*<\/p>/', $comments);
        $this->assertStringContainsString('1 removed', $comments);

        $goal = $this->scoreboardItem($html, 'Monthly goal');
        $this->assertStringContainsString('data-control="goal-meter" data-value="25"', $goal);
        $this->assertMatchesRegularExpression('/<p>\s*2 of 8\s*<\/p>/', $goal);
        $this->assertStringContainsString('6 to publish this month', $goal);
    }

    public function test_the_charts_show_views_by_week_from_the_first_post_and_posts_by_section(): void
    {
        $this->makeBlog();

        $html = $this->get(route('dashboard.index'))->assertOk()->getContent();

        // The first week holds the oldest post, and the last week is this one
        $firstWeek = Carbon::parse('2026-09-28', 'UTC')->getTimestampMs();
        $thisWeek = Carbon::parse('2026-11-16', 'UTC')->getTimestampMs();
        $this->assertMatchesRegularExpression('/data-set-name="Views"[^>]*data-set-data="\['.$firstWeek.',1200\],.*\['.$thisWeek.',900\]"/', $html);

        // A section counts the posts in the categories below it
        $this->assertStringContainsString('data-control="chart-bar"', $html);
        $this->assertMatchesRegularExpression('/Guides\s*<span>3<\/span>/', $html);
        $this->assertMatchesRegularExpression('/News\s*<span>3<\/span>/', $html);
    }

    public function test_the_most_read_list_shows_the_five_most_read_published_posts(): void
    {
        $posts = $this->makeBlog();
        Post::factory()->published()->count(4)->sequence(
            ['views' => 1000],
            ['views' => 800],
            ['views' => 700],
            ['views' => 100],
        )->create();

        $html = $this->get(route('dashboard.index'))->assertOk()->getContent();

        preg_match('/id="Lists-popularPosts".*?<\/table>/s', $html, $match);
        $this->assertNotEmpty($match, 'The most read list is missing.');

        $this->assertSame(5, substr_count($match[0], 'href="'.url('posts')));
        $this->assertMatchesRegularExpression('/Older Post.*?posts\/\d+\/edit.*?Recent Post/s', $match[0]);
        $this->assertStringNotContainsString('Draft Post', $match[0]);
        $this->assertStringNotContainsString('Earlier Post', $match[0]);
        $this->assertStringContainsString('href="'.url("posts/{$posts['older']->id}/edit").'"', $match[0]);
    }

    public function test_the_attention_list_counts_the_posts_to_review_finish_and_illustrate(): void
    {
        $posts = $this->makeBlog();

        $html = $this->get(route('dashboard.index'))->assertOk()->getContent();

        $this->assertStringContainsString('1 post waiting for review', $html);
        $this->assertStringContainsString('1 review due in the next two weeks', $html);
        $this->assertStringContainsString('1 draft in progress', $html);
        $this->assertStringContainsString('6 posts without a featured image', $html);
        $this->assertStringContainsString('href="'.route('posts.edit', $posts['recent']).'"', $html);
        $this->assertStringContainsString('1 day ago', $html);
    }

    public function test_an_empty_blog_shows_the_dashboard_without_figures(): void
    {
        $html = $this->get(route('dashboard.index'))->assertOk()->getContent();

        $this->assertStringContainsString('Nothing is waiting for review', $html);
        $this->assertStringContainsString('No posts are published yet.', $html);
        $this->assertStringNotContainsString('Last published', $html);
    }

    public function test_the_sidebar_links_the_dashboard_first_in_the_showcase(): void
    {
        $html = $this->get(route('posts.index'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/Showcase\s*<\/p>\s*<ul class="sidebar-nav-list">\s*<li[^>]*>\s*<a\s+class="sidebar-nav-link"\s+href="'.preg_quote(route('dashboard.index'), '/').'"/', $html);
    }
}
