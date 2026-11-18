<?php

namespace App\Controllers\Reference\ExtendingController;

use App\Models\Post;
use App\Widgets\CommentFeed;

/**
 * StandaloneWidgets builds the app's own widget on the Custom Widgets page.
 */
trait StandaloneWidgets
{
    /**
     * makeCommentFeedWidget builds the comment feed for the post with the most comments.
     */
    protected function makeCommentFeedWidget(): CommentFeed
    {
        $post = Post::query()->withCount('comments')->orderByDesc('comments_count')->first();

        return CommentFeed::make(
            post: $post ?? new Post,
            pageSize: 3,
        );
    }
}
