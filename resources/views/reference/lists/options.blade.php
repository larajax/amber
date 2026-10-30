@extends('layouts.app')
@section('title', 'List Options')
@section('content')

@include('reference._header', [
    'intro' => 'The options of <code>Lists::make()</code> sort, page and link a list, and let visitors check rows or choose columns.',
])

<section class="ui-section" id="sorting">
    @include('reference._heading', ['id' => 'sorting', 'title' => 'Sorting', 'caption' => '<code>defaultSort</code> and a column with <code>sortable: false</code>.'])
    @include('reference._widget_example', [
        'widgets' => [$sortingList],
        'methods' => ['makeSortingList'],
        'sources' => ['resources/ui/reference/lists/options/sorting.yaml'],
    ])
</section>

<section class="ui-section" id="pagination">
    @include('reference._heading', ['id' => 'pagination', 'title' => 'Pagination', 'caption' => '<code>recordsPerPage</code> with the page in the address through <code>customPageName</code>.'])
    @include('reference._widget_example', [
        'widgets' => [$pagedList],
        'methods' => ['makePagedList'],
        'sources' => ['resources/ui/reference/lists/options/posts.yaml'],
    ])
</section>

<section class="ui-section" id="simple-pagination">
    @include('reference._heading', ['id' => 'simple-pagination', 'title' => 'Simple pagination', 'caption' => '<code>showPageNumbers: false</code> pages without counting the records.'])
    @include('reference._widget_example', [
        'widgets' => [$simplePagedList],
        'methods' => ['makeSimplePagedList'],
    ])
</section>

<section class="ui-section" id="setup">
    @include('reference._heading', ['id' => 'setup', 'title' => 'List setup', 'caption' => '<code>showSetup</code> and <code>perPageOptions</code>, opened by the toolbar <code>setupHandler</code>.'])
    @include('reference._widget_example', [
        'widgets' => [$setupToolbar, $setupList],
        'methods' => ['makeSetupList', 'makeSetupToolbar'],
        'sources' => ['resources/ui/reference/lists/options/setup.yaml'],
    ])
</section>

<section class="ui-section" id="checkboxes">
    @include('reference._heading', ['id' => 'checkboxes', 'title' => 'Checkboxes', 'caption' => '<code>showCheckboxes</code> with a toolbar button that sends the checked rows.'])
    @include('reference._widget_example', [
        'widgets' => [$checkboxToolbar, $checkboxList],
        'methods' => ['makeCheckboxList', 'makeCheckboxToolbar', 'onShowCheckedPosts'],
    ])
</section>

<section class="ui-section" id="searchable">
    @include('reference._heading', ['id' => 'searchable', 'title' => 'Searchable columns and no records', 'caption' => '<code>searchable</code> columns and a <code>noRecordsMessage</code>.'])
    @include('reference._widget_example', [
        'widgets' => [$searchableToolbar, $searchableList],
        'methods' => ['makeSearchableList', 'makeSearchableToolbar'],
        'sources' => ['resources/ui/reference/lists/options/searchable.yaml'],
    ])
</section>

<section class="ui-section" id="record-url">
    @include('reference._heading', ['id' => 'record-url', 'title' => 'Record URL', 'caption' => '<code>recordUrl</code> with a <code>clickable: false</code> column.'])
    @include('reference._widget_example', [
        'widgets' => [$recordUrlList],
        'methods' => ['makeRecordUrlList'],
        'sources' => ['resources/ui/reference/lists/options/record-url.yaml'],
    ])
</section>

<section class="ui-section" id="record-on-click">
    @include('reference._heading', ['id' => 'record-on-click', 'title' => 'Record click and popups', 'caption' => '<code>recordOnClick: popup</code> with the <code>onLoadPopupForm</code> handler.'])
    @include('reference._widget_example', [
        'widgets' => [$popupList],
        'methods' => ['makePopupList', 'onLoadPopupForm'],
        'sources' => [
            'resources/ui/reference/lists/options/popup.yaml',
            'resources/views/reference/lists/options/_category_popup.blade.php',
        ],
    ])
</section>

<section class="ui-section" id="row-events">
    @include('reference._heading', ['id' => 'row-events', 'title' => 'Rows by record', 'caption' => 'The <code>list.injectRowClass</code> and <code>list.overrideRecordAction</code> events.'])
    @include('reference._widget_example', [
        'widgets' => [$rowEventsList],
        'methods' => ['makeRowEventsList'],
    ])
</section>

<section class="ui-section" id="rowlink">
    @include('reference._heading', ['id' => 'rowlink', 'title' => 'Row links on any table', 'caption' => 'The <code>rowlink</code> control with a <code>nolink</code> cell.'])
    @include('reference._example', [
        'view' => 'reference.lists.examples.rowlink',
        'stacked' => true,
    ])
</section>

@endsection
