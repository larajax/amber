<?php

namespace App\Controllers\Reference\FiltersController;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;
use Larajax\Ui\Widgets\Filter;
use Larajax\Ui\Widgets\Lists;
use Larajax\Ui\Widgets\Toolbar;

/**
 * OptionsExamples builds the filters on the Filter Options page, each bound to its own list.
 */
trait OptionsExamples
{
    /**
     * makeFilterOptionExamples builds every filter on the page with the list it filters.
     *
     * @return array<string, mixed>
     */
    protected function makeFilterOptionExamples(): array
    {
        $emptyOptionList = $this->makeFilteredPosts('emptyOptionList');
        $dependsList = $this->makeFilteredUsers();
        $searchList = $this->makeFilteredPosts('searchList');
        $primarySearchList = $this->makeFilteredPosts('primarySearchList');
        $primaryTabsList = $this->makeFilteredPosts('primaryTabsList');

        return [
            'examples' => $this->makeOptionFilters(),
            'emptyOptionList' => $emptyOptionList,
            'emptyOptionFilter' => $this->makeEmptyOptionFilter($emptyOptionList),
            'emptyOptionToolbar' => $this->makeEmptyOptionToolbar($emptyOptionList),
            'dependsList' => $dependsList,
            'dependsFilter' => $this->makeDependsFilter($dependsList),
            'searchList' => $searchList,
            'searchFilter' => $this->makeSearchFilter($searchList),
            'primarySearchList' => $primarySearchList,
            'primarySearchFilter' => $this->makePrimarySearchFilter($primarySearchList),
            'primaryTabsList' => $primaryTabsList,
            'primaryTabsFilter' => $this->makePrimaryTabsFilter($primaryTabsList),
        ];
    }

    // #region docs-option-filters
    /**
     * makeOptionFilters builds a filter for each example whose options are all in its YAML file, bound to a list of posts.
     *
     * @return array<string, array{filter: Filter, list: Lists}>
     */
    protected function makeOptionFilters(): array
    {
        $examples = [];

        foreach (['defaults', 'conditions', 'group-options', 'date-options', 'number-options', 'names'] as $name) {
            $list = $this->makeFilteredPosts(Str::camel($name).'List');

            $filter = Filter::make(
                alias: Str::camel($name).'Filter',
                model: new Post,
                scopes: "~/resources/ui/reference/filters/options/{$name}.yaml",
            );

            $filter->bindToListWidget($list);

            $examples[$name] = ['filter' => $filter, 'list' => $list];
        }

        return $examples;
    }

    /**
     * makeFilteredPosts builds a list of posts for a filter to filter.
     */
    protected function makeFilteredPosts(string $alias): Lists
    {
        return Lists::make(
            alias: $alias,
            model: new Post,
            columns: '~/resources/ui/reference/filters/posts.yaml',
            recordsPerPage: 5,
            noRecordsMessage: 'No posts match the filter.',
            customPageName: false,
        );
    }
    // #endregion docs-option-filters

    // #region docs-empty-option
    /**
     * makeEmptyOptionFilter filters by tag, offering an option for the untagged posts.
     */
    protected function makeEmptyOptionFilter(Lists $list): Filter
    {
        $filter = Filter::make(
            alias: 'emptyOptionFilter',
            model: new Post,
            scopes: '~/resources/ui/reference/filters/options/empty-option.yaml',
        );

        $filter->bindToListWidget($list);

        return $filter;
    }

    /**
     * makeEmptyOptionToolbar searches the same list as the filter.
     */
    protected function makeEmptyOptionToolbar(Lists $list): Toolbar
    {
        $toolbar = Toolbar::make(
            alias: 'emptyOptionToolbar',
            search: ['prompt' => 'Search posts...'],
        );

        $toolbar->bindToListWidget($list);

        return $toolbar;
    }
    // #endregion docs-empty-option

    // #region docs-depends
    /**
     * makeFilteredUsers builds a list of users for the dependent scopes to filter.
     */
    protected function makeFilteredUsers(): Lists
    {
        return Lists::make(
            alias: 'dependsList',
            model: new User,
            columns: '~/resources/ui/reference/filters/users.yaml',
            recordsPerPage: 5,
            noRecordsMessage: 'No users match the filter.',
            customPageName: false,
        );
    }

    /**
     * makeDependsFilter filters the users by a model scope and a scope that resets when it changes.
     */
    protected function makeDependsFilter(Lists $list): Filter
    {
        $filter = Filter::make(
            alias: 'dependsFilter',
            model: new User,
            scopes: '~/resources/ui/reference/filters/options/depends.yaml',
        );

        $filter->bindToListWidget($list);

        return $filter;
    }
    // #endregion docs-depends

    // #region docs-filter-search
    /**
     * makeSearchFilter adds a search box and the list setup icon after the scopes.
     */
    protected function makeSearchFilter(Lists $list): Filter
    {
        $filter = Filter::make(
            alias: 'searchFilter',
            model: new Post,
            scopes: '~/resources/ui/reference/filters/options/search.yaml',
            search: ['prompt' => 'Search posts...'],
            setupHandler: $list->getEventHandler('onLoadSetup'),
        );

        $filter->bindToListWidget($list);

        return $filter;
    }
    // #endregion docs-filter-search

    // #region docs-primary-search
    /**
     * makePrimarySearchFilter gives the search box and the list setup icon a row of their own above the scopes.
     */
    protected function makePrimarySearchFilter(Lists $list): Filter
    {
        $filter = Filter::make(
            alias: 'primarySearchFilter',
            model: new Post,
            scopes: '~/resources/ui/reference/filters/options/search.yaml',
            search: ['prompt' => 'Search posts...'],
            setupHandler: $list->getEventHandler('onLoadSetup'),
            primary: 'search',
        );

        $filter->bindToListWidget($list);

        return $filter;
    }
    // #endregion docs-primary-search

    // #region docs-primary-tabs
    /**
     * makePrimaryTabsFilter shows the status dropdown as tabs in a row above the other scopes.
     */
    protected function makePrimaryTabsFilter(Lists $list): Filter
    {
        $filter = Filter::make(
            alias: 'primaryTabsFilter',
            model: new Post,
            scopes: '~/resources/ui/reference/filters/options/primary-tabs.yaml',
            search: ['prompt' => 'Search posts...'],
            setupHandler: $list->getEventHandler('onLoadSetup'),
            primary: 'status',
        );

        $filter->bindToListWidget($list);

        return $filter;
    }
    // #endregion docs-primary-tabs
}
