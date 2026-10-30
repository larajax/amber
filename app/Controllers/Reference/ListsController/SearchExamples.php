<?php

namespace App\Controllers\Reference\ListsController;

use App\Models\Post;
use App\Models\Tag;
use Larajax\Classes\AjaxResponse;
use Larajax\Ui\Widgets\Lists;
use Larajax\Ui\Widgets\Search;
use Larajax\Ui\Widgets\Toolbar;

/**
 * SearchExamples builds the search widgets on the Search page and handles the search inputs.
 */
trait SearchExamples
{
    /**
     * makeSearchWidgets builds every search on the page, with the lists they search.
     *
     * @return array<string, Lists|Search|Toolbar|string>
     */
    protected function makeSearchWidgets(): array
    {
        $tagSearch = $this->makeTagSearch();
        $boundList = $this->makeSearchedList('boundList');
        $scopeList = $this->makeSearchedList('scopeList');
        $modeList = $this->makeSearchedList('modeList');
        $enterList = $this->makeSearchedList('enterList');

        return [
            'tagSearch' => $tagSearch,
            'tagResults' => $this->renderTagResults($tagSearch->getActiveTerm()),
            'boundList' => $boundList,
            'boundToolbar' => $this->makeBoundToolbar($boundList),
            'scopeList' => $scopeList,
            'scopeToolbar' => $this->makeScopeToolbar($scopeList),
            'modeList' => $modeList,
            'modeToolbar' => $this->makeModeToolbar($modeList),
            'enterList' => $enterList,
            'enterToolbar' => $this->makeEnterToolbar($enterList),
        ];
    }

    /**
     * makeTagSearch is a search box on its own, which updates the tag results through its search.submit event.
     */
    protected function makeTagSearch(): Search
    {
        $search = Search::make(
            alias: 'tagSearch',
            prompt: 'Search tags...',
            growable: false,
        );

        $search->bindEvent('search.submit', function () use ($search) {
            return ['#tagResults' => $this->renderTagResults($search->getActiveTerm())];
        });

        return $search;
    }

    /**
     * renderTagResults renders the tags whose name contains the term.
     */
    protected function renderTagResults(string $term): string
    {
        return view('reference.lists.search._tag_results', [
            'tags' => Tag::where('name', 'like', "%{$term}%")->withCount('posts')->orderBy('name')->get(),
            'term' => $term,
        ])->render();
    }

    /**
     * makeSearchedList builds a list of posts for a toolbar search to search.
     */
    protected function makeSearchedList(string $alias): Lists
    {
        return Lists::make(
            alias: $alias,
            model: new Post,
            columns: '~/resources/ui/reference/lists/search/posts.yaml',
            recordsPerPage: 4,
            noRecordsMessage: 'No posts match the search.',
            customPageName: false,
        );
    }

    /**
     * makeBoundToolbar searches the given list as you type.
     */
    protected function makeBoundToolbar(Lists $list): Toolbar
    {
        $toolbar = Toolbar::make(
            alias: 'boundToolbar',
            search: ['prompt' => 'Search posts...'],
        );

        $toolbar->bindToListWidget($list);

        return $toolbar;
    }

    /**
     * makeScopeToolbar searches the given list through the post model's applySearch scope.
     */
    protected function makeScopeToolbar(Lists $list): Toolbar
    {
        $toolbar = Toolbar::make(
            alias: 'scopeToolbar',
            search: [
                'prompt' => 'Search posts, categories and tags...',
                'scope' => 'applySearch',
            ],
        );

        $toolbar->bindToListWidget($list);

        return $toolbar;
    }

    /**
     * makeModeToolbar finds the posts matching any of the words.
     */
    protected function makeModeToolbar(Lists $list): Toolbar
    {
        $toolbar = Toolbar::make(
            alias: 'modeToolbar',
            search: [
                'prompt' => 'Search any word...',
                'mode' => 'any',
            ],
        );

        $toolbar->bindToListWidget($list);

        return $toolbar;
    }

    /**
     * makeEnterToolbar searches when Enter is pressed, in a box that keeps its width.
     */
    protected function makeEnterToolbar(Lists $list): Toolbar
    {
        $toolbar = Toolbar::make(
            alias: 'enterToolbar',
            search: [
                'prompt' => 'Type, then press Enter...',
                'searchOnEnter' => true,
                'growable' => false,
            ],
        );

        $toolbar->bindToListWidget($list);

        return $toolbar;
    }

    /**
     * onSearchCities filters the city list by the search input and updates the results.
     */
    public function onSearchCities(): AjaxResponse
    {
        $term = trim((string) input('city_search'));

        $cities = collect($this->getCityNames())
            ->filter(fn (string $city) => $term === '' || str_contains(mb_strtolower($city), mb_strtolower($term)))
            ->values()
            ->all();

        return ajax()->update([
            '#cityResults' => view('reference.lists.search._city_results', [
                'cities' => $cities,
                'term' => $term,
            ]),
        ]);
    }

    /**
     * getCityNames returns the cities the search input filters.
     *
     * @return array<int, string>
     */
    protected function getCityNames(): array
    {
        return [
            'Amsterdam', 'Auckland', 'Barcelona', 'Berlin', 'Brisbane', 'Buenos Aires', 'Cape Town', 'Lisbon',
            'Melbourne', 'Montreal', 'Osaka', 'Paris', 'Perth', 'Seoul', 'Sydney', 'Toronto', 'Vancouver',
        ];
    }
}
