<?php

namespace App\Controllers\DashboardController;

use App\Models\Post;
use Larajax\Ui\Widgets\Lists;

/**
 * PopularPostList builds the short list of the most read posts on the dashboard.
 */
trait PopularPostList
{
    /**
     * makePopularPostList builds a list of the five most read published posts, with no sorting or pagination, where clicking a row opens the post.
     */
    protected function makePopularPostList(): Lists
    {
        $list = Lists::make(
            alias: 'popularPosts',
            model: new Post,
            columns: '~/resources/ui/post/columns_popular.yaml',
            showPagination: false,
            showSorting: false,
            recordUrl: 'posts/:id/edit',
            defaultSort: ['column' => 'views', 'direction' => 'desc'],
            noRecordsMessage: 'No posts are published yet.',
        );

        // Without pagination the list reads every matching record, so the filter limits it to five
        $list->addFilter(fn ($query) => $query->where('published', true)->limit(5));

        return $list;
    }
}
