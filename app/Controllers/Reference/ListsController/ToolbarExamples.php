<?php

namespace App\Controllers\Reference\ListsController;

use App\Models\Post;
use Illuminate\Support\Str;
use Larajax\Classes\AjaxResponse;
use Larajax\Ui\Widgets\Lists;
use Larajax\Ui\Widgets\Toolbar;

/**
 * ToolbarExamples builds the toolbars on the Toolbar page and handles their buttons, which only report what they would do.
 */
trait ToolbarExamples
{
    /**
     * makeToolbarWidgets builds every toolbar on the page, each with the list it works on.
     *
     * @return array<string, Lists|Toolbar>
     */
    protected function makeToolbarWidgets(): array
    {
        $buttonsList = $this->makeToolbarList('buttonsList');
        $partialList = $this->makeToolbarList('partialList');
        $searchList = $this->makeToolbarList('searchList');

        return [
            'buttonsList' => $buttonsList,
            'buttonsToolbar' => $this->makeButtonsToolbar($buttonsList),
            'partialList' => $partialList,
            'partialToolbar' => $this->makePartialToolbar($partialList),
            'searchList' => $searchList,
            'searchToolbar' => $this->makeSearchToolbar($searchList),
            'overflowToolbar' => $this->makeOverflowToolbar(),
        ];
    }

    // #region docs-toolbar-list
    /**
     * makeToolbarList builds a list of posts with checkboxes for a toolbar to work on.
     */
    protected function makeToolbarList(string $alias): Lists
    {
        return Lists::make(
            alias: $alias,
            model: new Post,
            columns: '~/resources/ui/reference/lists/toolbar/columns.yaml',
            recordsPerPage: 5,
            showCheckboxes: true,
            showSetup: true,
            customPageName: false,
        );
    }
    // #endregion docs-toolbar-list

    // #region docs-buttons-toolbar
    /**
     * makeButtonsToolbar declares its buttons in a YAML file and links them to the given list.
     */
    protected function makeButtonsToolbar(Lists $list): Toolbar
    {
        $toolbar = Toolbar::make(
            alias: 'buttonsToolbar',
            buttons: '~/resources/ui/reference/lists/toolbar/buttons.yaml',
        );

        $toolbar->bindToListWidget($list);

        return $toolbar;
    }
    // #endregion docs-buttons-toolbar

    // #region docs-partial-toolbar
    /**
     * makePartialToolbar renders its buttons from a partial and links them to the given list.
     */
    protected function makePartialToolbar(Lists $list): Toolbar
    {
        $toolbar = Toolbar::make(
            alias: 'partialToolbar',
            buttons: '~/resources/views/reference/lists/toolbar/_list_toolbar.php',
        );

        $toolbar->bindToListWidget($list);

        return $toolbar;
    }
    // #endregion docs-partial-toolbar

    // #region docs-search-toolbar
    /**
     * makeSearchToolbar adds a search box and the list setup icon for the given list.
     */
    protected function makeSearchToolbar(Lists $list): Toolbar
    {
        $toolbar = Toolbar::make(
            alias: 'searchToolbar',
            buttons: [
                'create' => [
                    'label' => 'New Post',
                    'icon' => 'ph ph-plus',
                    'type' => 'primary',
                    'url' => 'posts/create',
                ],
            ],
            search: ['prompt' => 'Search posts...'],
            setupHandler: $list->getEventHandler('onLoadSetup'),
        );

        $toolbar->bindToListWidget($list);

        return $toolbar;
    }
    // #endregion docs-search-toolbar

    // #region docs-overflow-toolbar
    /**
     * makeOverflowToolbar has more buttons than fit its width, each sending its name as request data.
     */
    protected function makeOverflowToolbar(): Toolbar
    {
        return Toolbar::make(
            alias: 'overflowToolbar',
            buttons: '~/resources/ui/reference/lists/toolbar/overflow.yaml',
        );
    }
    // #endregion docs-overflow-toolbar

    // #region docs-button-handlers
    /**
     * onLoadToolbarDialog returns the dialog opened by the popup buttons.
     */
    public function onLoadToolbarDialog(): string
    {
        return view('reference.lists.toolbar._dialog')->render();
    }

    /**
     * onRefreshToolbarList reloads the list of the buttons example.
     */
    public function onRefreshToolbarList(): AjaxResponse
    {
        return ajax()
            ->flash('info', 'The list was refreshed.')
            ->update($this->getComponentInstance('buttonsList')->onRefresh());
    }

    /**
     * onCountCheckedPosts counts the checked posts and their views, as the checked buttons ask.
     */
    public function onCountCheckedPosts(): AjaxResponse
    {
        $posts = Post::whereIn('id', (array) input('checked'))->get();

        return ajax()->flash('info', $posts->count().' '.Str::plural('post', $posts->count()).' checked, with '.number_format($posts->sum('views')).' views.');
    }

    /**
     * onDeleteCheckedPosts reports the checked posts it would delete, leaving the seeded posts in place.
     */
    public function onDeleteCheckedPosts(): AjaxResponse
    {
        $count = Post::whereIn('id', (array) input('checked'))->count();

        return ajax()->flash('warning', $count.' '.Str::plural('post', $count).' would be deleted. The reference pages leave the posts in place.');
    }
    // #endregion docs-button-handlers

    // #region docs-toolbar-button
    /**
     * onToolbarButton names the button that sent the request, from its request data.
     */
    public function onToolbarButton(): AjaxResponse
    {
        return ajax()->flash('info', 'The '.Str::headline((string) input('button')).' button was clicked.');
    }
    // #endregion docs-toolbar-button
}
