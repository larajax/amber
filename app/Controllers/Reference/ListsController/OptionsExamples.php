<?php

namespace App\Controllers\Reference\ListsController;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Str;
use Larajax\Classes\AjaxResponse;
use Larajax\Ui\Widgets\Lists;
use Larajax\Ui\Widgets\Toolbar;

/**
 * OptionsExamples builds the lists on the List Options page and handles their requests.
 */
trait OptionsExamples
{
    /**
     * makeOptionWidgets builds every list on the page, with the toolbars that go with them.
     *
     * @return array<string, Lists|Toolbar>
     */
    protected function makeOptionWidgets(): array
    {
        $setupList = $this->makeSetupList();
        $checkboxList = $this->makeCheckboxList();
        $searchableList = $this->makeSearchableList();

        return [
            'sortingList' => $this->makeSortingList(),
            'pagedList' => $this->makePagedList(),
            'simplePagedList' => $this->makeSimplePagedList(),
            'setupList' => $setupList,
            'setupToolbar' => $this->makeSetupToolbar($setupList),
            'checkboxList' => $checkboxList,
            'checkboxToolbar' => $this->makeCheckboxToolbar($checkboxList),
            'searchableList' => $searchableList,
            'searchableToolbar' => $this->makeSearchableToolbar($searchableList),
            'recordUrlList' => $this->makeRecordUrlList(),
            'popupList' => $this->makePopupList(),
            'rowEventsList' => $this->makeRowEventsList(),
        ];
    }

    // #region docs-sorting
    /**
     * makeSortingList starts with the most viewed posts until a header is clicked.
     */
    protected function makeSortingList(): Lists
    {
        return Lists::make(
            alias: 'sortingList',
            model: new Post,
            columns: '~/resources/ui/reference/lists/options/sorting.yaml',
            recordsPerPage: 5,
            defaultSort: ['column' => 'views', 'direction' => 'desc'],
            customPageName: false,
        );
    }
    // #endregion docs-sorting

    // #region docs-pagination
    /**
     * makePagedList shows five posts a page, with the page number in the address.
     */
    protected function makePagedList(): Lists
    {
        return Lists::make(
            alias: 'pagedList',
            model: new Post,
            columns: '~/resources/ui/reference/lists/options/posts.yaml',
            recordsPerPage: 5,
            customPageName: 'postsPage',
        );
    }
    // #endregion docs-pagination

    // #region docs-simple-pagination
    /**
     * makeSimplePagedList pages without counting the records.
     */
    protected function makeSimplePagedList(): Lists
    {
        return Lists::make(
            alias: 'simplePagedList',
            model: new Post,
            columns: '~/resources/ui/reference/lists/options/posts.yaml',
            recordsPerPage: 5,
            showPageNumbers: false,
            customPageName: false,
        );
    }
    // #endregion docs-simple-pagination

    // #region docs-setup
    /**
     * makeSetupList lets the visitor pick the columns and the page size.
     */
    protected function makeSetupList(): Lists
    {
        return Lists::make(
            alias: 'setupList',
            model: new Post,
            columns: '~/resources/ui/reference/lists/options/setup.yaml',
            recordsPerPage: 5,
            perPageOptions: [5, 10, 20],
            showSetup: true,
            customPageName: false,
        );
    }

    /**
     * makeSetupToolbar shows the list setup icon, which opens the setup of the given list.
     */
    protected function makeSetupToolbar(Lists $list): Toolbar
    {
        return Toolbar::make(
            alias: 'setupToolbar',
            setupHandler: $list->getEventHandler('onLoadSetup'),
        );
    }
    // #endregion docs-setup

    // #region docs-checkboxes
    /**
     * makeCheckboxList adds a checkbox to each row.
     */
    protected function makeCheckboxList(): Lists
    {
        return Lists::make(
            alias: 'checkboxList',
            model: new Post,
            columns: '~/resources/ui/reference/lists/options/posts.yaml',
            recordsPerPage: 5,
            showCheckboxes: true,
            customPageName: false,
        );
    }

    /**
     * makeCheckboxToolbar has a button that sends the checked rows of the given list.
     */
    protected function makeCheckboxToolbar(Lists $list): Toolbar
    {
        $toolbar = Toolbar::make(
            alias: 'checkboxToolbar',
            buttons: [
                'show' => [
                    'label' => 'Show checked',
                    'icon' => 'ph ph-list-checks',
                    'handler' => 'onShowCheckedPosts',
                    'checked' => true,
                    'attributes' => ['data-request-flash' => true],
                ],
            ],
        );

        $toolbar->bindToListWidget($list);

        return $toolbar;
    }
    // #endregion docs-checkboxes

    // #region docs-searchable
    /**
     * makeSearchableList searches the columns marked searchable.
     */
    protected function makeSearchableList(): Lists
    {
        return Lists::make(
            alias: 'searchableList',
            model: new Post,
            columns: '~/resources/ui/reference/lists/options/searchable.yaml',
            recordsPerPage: 5,
            noRecordsMessage: 'No posts match the search.',
            customPageName: false,
        );
    }

    /**
     * makeSearchableToolbar has the search box that searches the given list.
     */
    protected function makeSearchableToolbar(Lists $list): Toolbar
    {
        $toolbar = Toolbar::make(
            alias: 'searchableToolbar',
            search: ['prompt' => 'Search posts...'],
        );

        $toolbar->bindToListWidget($list);

        return $toolbar;
    }
    // #endregion docs-searchable

    // #region docs-record-url
    /**
     * makeRecordUrlList links each row to the post's edit page.
     */
    protected function makeRecordUrlList(): Lists
    {
        return Lists::make(
            alias: 'recordUrlList',
            model: new Post,
            columns: '~/resources/ui/reference/lists/options/record-url.yaml',
            recordsPerPage: 5,
            recordUrl: 'posts/:id/edit',
            customPageName: false,
        );
    }
    // #endregion docs-record-url

    // #region docs-popup
    /**
     * makePopupList opens each category in a popup, showing every category on one page.
     */
    protected function makePopupList(): Lists
    {
        return Lists::make(
            alias: 'popupList',
            model: new Category,
            columns: '~/resources/ui/reference/lists/options/popup.yaml',
            recordOnClick: 'popup',
        );
    }
    // #endregion docs-popup

    // #region docs-row-events
    /**
     * makeRowEventsList fades the archived posts and leaves them out of the row link.
     */
    protected function makeRowEventsList(): Lists
    {
        $list = Lists::make(
            alias: 'rowEventsList',
            model: new Post,
            columns: '~/resources/ui/reference/lists/options/posts.yaml',
            recordsPerPage: 10,
            recordUrl: 'posts/:id/edit',
            customPageName: false,
        );

        $list->bindEvent('list.injectRowClass', function (Post $post) {
            return $post->status === 'archived' ? 'opacity-50' : null;
        });

        $list->bindEvent('list.overrideRecordAction', function (Post $post) {
            return $post->status === 'archived' ? ['clickable' => false] : null;
        });

        return $list;
    }
    // #endregion docs-row-events

    // #region docs-checked-posts
    /**
     * onShowCheckedPosts names the posts checked in the list, as sent by the toolbar button.
     */
    public function onShowCheckedPosts(): AjaxResponse
    {
        $titles = Post::whereIn('id', (array) input('checked'))->orderBy('title')->pluck('title');

        return ajax()->flash('info', $titles->count().' '.Str::plural('post', $titles->count()).' checked: '.$titles->implode(', ').'.');
    }
    // #endregion docs-checked-posts

    // #region docs-popup-form
    /**
     * onLoadPopupForm shows the category of the clicked row, as the handler the popup record click opens.
     */
    public function onLoadPopupForm(): string
    {
        return view('reference.lists.options._category_popup', [
            'category' => Category::with('posts')->findOrFail(input('form_record_id')),
        ])->render();
    }
    // #endregion docs-popup-form

    // #region docs-rowlink-handlers
    /**
     * onLoadRowDialog shows the dialog opened by the popup row of the row link table.
     */
    public function onLoadRowDialog(): string
    {
        return view('reference.lists.options._row_dialog')->render();
    }

    /**
     * onRowAction handles the row of the row link table that runs an AJAX request.
     */
    public function onRowAction(): AjaxResponse
    {
        return ajax()->flash('info', 'The row ran an AJAX handler.');
    }
    // #endregion docs-rowlink-handlers
}
