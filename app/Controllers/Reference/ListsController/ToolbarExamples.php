<?php

namespace App\Controllers\Reference\ListsController;

use App\Models\Post;
use Illuminate\Support\Str;
use Larajax\Classes\AjaxResponse;
use Larajax\Ui\Classes\ToolbarButton;
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

        $menusList = $this->makeToolbarList('menusList');
        $splitList = $this->makeToolbarList('splitList');

        return [
            'buttonsList' => $buttonsList,
            'buttonsToolbar' => $this->makeButtonsToolbar($buttonsList),
            'partialList' => $partialList,
            'partialToolbar' => $this->makePartialToolbar($partialList),
            'searchList' => $searchList,
            'searchToolbar' => $this->makeSearchToolbar($searchList),
            'overflowToolbar' => $this->makeOverflowToolbar(),
            'menusList' => $menusList,
            'menusToolbar' => $this->makeMenusToolbar($menusList),
            'splitList' => $splitList,
            'splitToolbar' => $this->makeSplitToolbar($splitList),
            'groupedToolbar' => $this->makeGroupedToolbar(),
            'extendedToolbar' => $this->makeExtendedToolbar(),
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

    // #region docs-menus-toolbar
    /**
     * makeMenusToolbar declares dropdown menus in YAML, one of them following the checked rows of the given list.
     */
    protected function makeMenusToolbar(Lists $list): Toolbar
    {
        $toolbar = Toolbar::make(
            alias: 'menusToolbar',
            buttons: '~/resources/ui/reference/lists/toolbar/menus.yaml',
        );

        $toolbar->bindToListWidget($list);

        return $toolbar;
    }
    // #endregion docs-menus-toolbar

    // #region docs-split-toolbar
    /**
     * makeSplitToolbar declares split buttons and a button for exactly one checked row of the given list.
     */
    protected function makeSplitToolbar(Lists $list): Toolbar
    {
        $toolbar = Toolbar::make(
            alias: 'splitToolbar',
            buttons: '~/resources/ui/reference/lists/toolbar/split.yaml',
        );

        $toolbar->bindToListWidget($list);

        return $toolbar;
    }
    // #endregion docs-split-toolbar

    // #region docs-grouped-toolbar
    /**
     * makeGroupedToolbar declares a button group, a divider, a disabled button, a partial and a menu at the end.
     */
    protected function makeGroupedToolbar(): Toolbar
    {
        return Toolbar::make(
            alias: 'groupedToolbar',
            buttons: '~/resources/ui/reference/lists/toolbar/grouped.yaml',
        );
    }

    /**
     * isToolbarViewActive marks the List button of the view group as the current view.
     */
    public function isToolbarViewActive(ToolbarButton $button): bool
    {
        return $button->name === 'list';
    }
    // #endregion docs-grouped-toolbar

    // #region docs-extended-toolbar
    /**
     * makeExtendedToolbar changes the buttons of its YAML file when they are defined, placing new ones by name.
     */
    protected function makeExtendedToolbar(): Toolbar
    {
        $toolbar = Toolbar::make(
            alias: 'extendedToolbar',
            buttons: '~/resources/ui/reference/lists/toolbar/extend.yaml',
        );

        $toolbar->bindEvent('toolbar.extendButtons', function (Toolbar $toolbar) {
            $toolbar->addButtons([
                'preview' => [
                    'label' => 'Preview',
                    'icon' => 'ph ph-app-window',
                    'handler' => 'onLoadToolbarDialog',
                    'popup' => true,
                ],
            ], before: 'actions');

            $toolbar->addButtons([
                'feature' => [
                    'label' => 'Feature',
                    'handler' => 'onToolbarButton',
                    'requestData' => ['button' => 'feature'],
                    'attributes' => ['data-request-flash' => true],
                ],
            ], after: 'actions.publish');

            $toolbar->removeButton('actions.archive');

            $toolbar->getButton('actions')->label('More');
        });

        return $toolbar;
    }
    // #endregion docs-extended-toolbar

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
        $posts = Post::whereIn('id', (array) request()->input('checked'))->get();

        return ajax()->flash('info', $posts->count().' '.Str::plural('post', $posts->count()).' checked, with '.number_format($posts->sum('views')).' views.');
    }

    /**
     * onDeleteCheckedPosts reports the checked posts it would delete, leaving the seeded posts in place.
     */
    public function onDeleteCheckedPosts(): AjaxResponse
    {
        $count = Post::whereIn('id', (array) request()->input('checked'))->count();

        return ajax()->flash('warning', $count.' '.Str::plural('post', $count).' would be deleted. The reference pages leave the posts in place.');
    }
    // #endregion docs-button-handlers

    // #region docs-toolbar-button
    /**
     * onToolbarButton names the button that sent the request, from its request data.
     */
    public function onToolbarButton(): AjaxResponse
    {
        return ajax()->flash('info', 'The '.Str::headline((string) request()->input('button')).' button was clicked.');
    }
    // #endregion docs-toolbar-button

    // #region docs-checked-button
    /**
     * onCheckedToolbarButton names the menu item that sent the request and counts the checked posts it received.
     */
    public function onCheckedToolbarButton(): AjaxResponse
    {
        $count = count((array) request()->input('checked'));

        return ajax()->flash('info', Str::headline((string) request()->input('button')).' would apply to '.$count.' checked '.Str::plural('post', $count).'.');
    }

    /**
     * onShowCheckedPost names the one checked post, as the button allows a single row.
     */
    public function onShowCheckedPost(): AjaxResponse
    {
        $post = Post::find(head((array) request()->input('checked')));

        return ajax()->flash('info', $post ? 'The checked post is "'.$post->title.'".' : 'No post is checked.');
    }
    // #endregion docs-checked-button
}
