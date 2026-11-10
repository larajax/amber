<?php

namespace App\Controllers\DashboardController;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * DashboardFigures counts the figures behind the dashboard's scoreboard, its charts and its list of posts that need attention.
 */
trait DashboardFigures
{
    /**
     * @var int monthlyGoal is how many posts the blog aims to publish each month.
     */
    protected int $monthlyGoal = 8;

    /**
     * prepareFigureVars counts every figure for the index view.
     *
     * @return array<string, mixed>
     */
    protected function prepareFigureVars(): array
    {
        $published = Post::query()->where('published', true)->get(['id', 'title', 'published_at', 'views']);
        $weeks = $this->getRecentWeeks(16);
        $publishedThisMonth = $published->where('published_at', '>=', now()->startOfMonth())->count();

        // The chart starts at the week of the first post, without empty weeks before it
        $firstWeek = $published->sortBy('published_at')->first()?->published_at->copy()->startOfWeek();
        $chartWeeks = $firstWeek ? $weeks->filter(fn (Carbon $week) => $week->gte($firstWeek))->values() : $weeks;

        return [
            'statusValues' => $this->getStatusValues(),
            'published' => [
                'count' => $published->count(),
                'compare' => [
                    $published->whereBetween('published_at', [now()->subDays(30), now()])->count(),
                    $published->whereBetween('published_at', [now()->subDays(60), now()->subDays(30)])->count(),
                ],
                'sparkline' => array_values($this->sumByWeek($published, $weeks->slice(-8), fn () => 1)),
            ],
            'views' => [
                'total' => (int) $published->sum('views'),
                'average' => (int) round($published->avg('views') ?? 0),
                'sparkline' => array_values($this->sumByWeek($published, $weeks->slice(-8), fn (Post $post) => $post->views)),
            ],
            'comments' => [
                'count' => Comment::query()->count(),
                'removed' => Comment::onlyTrashed()->count(),
            ],
            'goal' => [
                'count' => $publishedThisMonth,
                'target' => $this->monthlyGoal,
                'meter' => min(100, (int) round($publishedThisMonth / $this->monthlyGoal * 100)),
            ],
            'weeklyViews' => $this->sumByWeek($published, $chartWeeks, fn (Post $post) => $post->views),
            'sectionValues' => $this->getSectionValues(),
            'attention' => [
                'inReview' => Post::query()->where('status', 'review')->count(),
                'drafts' => Post::query()->where('status', 'draft')->count(),
                'reviewsDue' => Post::query()->whereBetween('review_on', [today()->toDateString(), today()->addDays(14)->toDateString()])->count(),
                'withoutImage' => Post::query()->whereDoesntHave('featured_image')->count(),
                'lastPublished' => $published->sortByDesc('published_at')->first(),
            ],
        ];
    }

    /**
     * getStatusValues counts the posts in each status but archived, labelled and colored as the posts list shows them, where the scoreboard's pie has room for three.
     *
     * @return array<string, array{0: int, 1: string}>
     */
    protected function getStatusValues(): array
    {
        $counts = Post::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        $values = [];

        foreach (array_diff_key(Post::listStatusColorOptions(), ['archived' => true]) as $status => [$label, $color]) {
            $values[$label] = [(int) ($counts[$status] ?? 0), $color];
        }

        return $values;
    }

    /**
     * getSectionValues counts the posts in each top level category, with the categories below it.
     *
     * @return array<string, int>
     */
    protected function getSectionValues(): array
    {
        return Category::query()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get()
            ->mapWithKeys(fn (Category $section) => [
                $section->name => Post::query()->whereIn('category_id', $section->getAllChildren()->push($section)->pluck('id'))->count(),
            ])
            ->all();
    }

    /**
     * getRecentWeeks returns the first day of each of the given number of weeks, ending with this week.
     *
     * @return Collection<int, Carbon>
     */
    protected function getRecentWeeks(int $count): Collection
    {
        return collect(range($count - 1, 0))->map(fn (int $weeksAgo) => now()->startOfWeek()->subWeeks($weeksAgo));
    }

    /**
     * sumByWeek adds up a value of the posts published in each week, keyed by the date the week starts.
     *
     * @param  Collection<int, Post>  $posts
     * @param  Collection<int, Carbon>  $weeks
     * @return array<string, int>
     */
    protected function sumByWeek(Collection $posts, Collection $weeks, callable $value): array
    {
        return $weeks
            ->mapWithKeys(fn (Carbon $week) => [
                $week->toDateString() => (int) $posts
                    ->filter(fn (Post $post) => $post->published_at?->between($week, $week->copy()->endOfWeek()))
                    ->sum($value),
            ])
            ->all();
    }
}
