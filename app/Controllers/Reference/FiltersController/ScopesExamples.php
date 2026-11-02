<?php

namespace App\Controllers\Reference\FiltersController;

use App\Models\Comment;
use App\Models\Post;
use Larajax\Ui\Widgets\Filter;
use Larajax\Ui\Widgets\Lists;

/**
 * ScopesExamples builds the filters on the Scope Types page, each bound to its own list of posts.
 */
trait ScopesExamples
{
    /**
     * makeScopeExamples builds a filter for each scope type example, with the scopes from its YAML file, bound to a list of posts.
     *
     * @return array<string, array{filter: Filter, list: Lists}>
     */
    protected function makeScopeExamples(): array
    {
        $examples = [];

        foreach (['checkbox', 'switch', 'dropdown', 'group', 'date', 'text', 'number'] as $name) {
            $list = Lists::make(
                alias: $name.'List',
                model: new Post,
                columns: '~/resources/ui/reference/filters/posts.yaml',
                recordsPerPage: 5,
                noRecordsMessage: 'No posts match the filter.',
                customPageName: false,
            );

            $filter = Filter::make(
                alias: $name.'Filter',
                model: new Post,
                scopes: "~/resources/ui/reference/filters/scopes/{$name}.yaml",
            );

            $filter->bindToListWidget($list);

            $examples[$name] = ['filter' => $filter, 'list' => $list];
        }

        $examples['trashed'] = $this->makeTrashedExample();

        return $examples;
    }

    // #region docs-trashed
    /**
     * makeTrashedExample builds a filter that shows the soft deleted comments, bound to a list of comments.
     *
     * @return array{filter: Filter, list: Lists}
     */
    protected function makeTrashedExample(): array
    {
        $list = Lists::make(
            alias: 'trashedList',
            model: new Comment,
            columns: '~/resources/ui/reference/filters/comments.yaml',
            recordsPerPage: 5,
            noRecordsMessage: 'No comments match the filter.',
            customPageName: false,
        );

        $filter = Filter::make(
            alias: 'trashedFilter',
            model: new Comment,
            scopes: '~/resources/ui/reference/filters/scopes/trashed.yaml',
        );

        $filter->bindToListWidget($list);

        return ['filter' => $filter, 'list' => $list];
    }
    // #endregion docs-trashed
}
