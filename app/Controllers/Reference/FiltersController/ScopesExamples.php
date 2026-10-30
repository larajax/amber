<?php

namespace App\Controllers\Reference\FiltersController;

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

        return $examples;
    }
}
