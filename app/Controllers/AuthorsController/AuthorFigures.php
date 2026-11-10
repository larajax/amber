<?php

namespace App\Controllers\AuthorsController;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

/**
 * AuthorFigures counts the figures in the scoreboard above an author's form.
 */
trait AuthorFigures
{
    /**
     * prepareAuthorFigureVars counts the author's posts, their views and comments, and finds their latest post, for the edit view.
     *
     * @return array{figures: array<string, mixed>}
     */
    protected function prepareAuthorFigureVars(User $user): array
    {
        $posts = Post::query()->where('author_id', $user->id)->get(['id', 'title', 'published', 'published_at', 'views']);
        $published = $posts->where('published', true)->sortBy('published_at');

        return [
            'figures' => [
                'postCount' => $posts->count(),
                'publishedCount' => $published->count(),
                'views' => (int) $published->sum('views'),
                'averageViews' => (int) round($published->avg('views') ?? 0),
                'viewsByPost' => $published->take(-8)->pluck('views')->map(fn ($views) => (int) $views)->values()->all(),
                'comments' => Comment::query()->whereIn('post_id', $posts->pluck('id'))->count(),
                'latestPost' => $published->last(),
            ],
        ];
    }
}
