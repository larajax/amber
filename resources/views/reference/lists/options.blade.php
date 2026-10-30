@extends('layouts.app')
@section('title', 'List Options')
@section('content')

@include('reference._header', [
    'intro' => 'The options of <code>Lists::make()</code> decide how a list sorts, pages, links its rows and lets the visitor check rows or choose the columns. Each example shows the method that builds the list and the YAML of its columns. Every list here except one sets <code>customPageName: false</code>, so the lists on the page do not share a page number.',
])

<section class="ui-section" id="sorting">
    <h2>
        Sorting
    </h2>
    <p class="fg-3">
        Clicking a header sorts the list by that column, a second click reverses it, and the list remembers the choice for the session. <code>defaultSort</code> sets the order until then. A column with <code>sortable: false</code> has a plain header, and <code>showSorting: false</code> turns sorting off for the whole list.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$sortingList],
        'methods' => ['makeSortingList'],
        'sources' => ['resources/ui/reference/lists/options/sorting.yaml'],
    ])
</section>

<section class="ui-section" id="pagination">
    <h2>
        Pagination
    </h2>
    <p class="fg-3">
        <code>recordsPerPage</code> splits the list into pages, with the record count and a link for each page. The dots open a box to type a page number. <code>customPageName</code> names the page in the address, here <code>?postsPage=2</code>, so a reload keeps the page.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$pagedList],
        'methods' => ['makePagedList'],
        'sources' => ['resources/ui/reference/lists/options/posts.yaml'],
    ])
</section>

<section class="ui-section" id="simple-pagination">
    <h2>
        Simple pagination
    </h2>
    <p class="fg-3">
        <code>showPageNumbers: false</code> leaves out the record count and the page links, keeping only first, previous and next, so the list never counts the records. It suits large tables.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$simplePagedList],
        'methods' => ['makeSimplePagedList'],
    ])
</section>

<section class="ui-section" id="setup">
    <h2>
        List setup
    </h2>
    <p class="fg-3">
        <code>showSetup</code> keeps the visitor's choice of columns and page size for the session, and a toolbar's <code>setupHandler</code> shows the icon that opens the setup. In it, drag the columns into a new order and pick a page size from <code>perPageOptions</code>. Columns marked <code>invisible</code> start unchecked, while a <code>hidden</code> column is never shown, not even in the setup.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$setupToolbar, $setupList],
        'methods' => ['makeSetupList', 'makeSetupToolbar'],
        'sources' => ['resources/ui/reference/lists/options/setup.yaml'],
    ])
</section>

<section class="ui-section" id="checkboxes">
    <h2>
        Checkboxes
    </h2>
    <p class="fg-3">
        <code>showCheckboxes</code> adds a checkbox to each row and one in the header that checks them all. Hold Shift to check a range. The toolbar button with <code>checked: true</code> is enabled while rows are checked and sends their ids to its handler as <code>checked</code>.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$checkboxToolbar, $checkboxList],
        'methods' => ['makeCheckboxList', 'makeCheckboxToolbar', 'onShowCheckedPosts'],
    ])
</section>

<section class="ui-section" id="searchable">
    <h2>
        Searchable columns and no records
    </h2>
    <p class="fg-3">
        A bound search box looks in the columns marked <code>searchable</code>, including a relation column and the <code>invisible</code> excerpt. Search for "nested" to match a title, "releases" to match a category, or a word that no post has to see <code>noRecordsMessage</code>.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$searchableToolbar, $searchableList],
        'methods' => ['makeSearchableList', 'makeSearchableToolbar'],
        'sources' => ['resources/ui/reference/lists/options/searchable.yaml'],
    ])
</section>

<section class="ui-section" id="record-url">
    <h2>
        Record URL
    </h2>
    <p class="fg-3">
        <code>recordUrl</code> links every row to an address, where <code>:id</code> and other attributes take the record's values. The whole row follows the link, except cells with <code>clickable: false</code>, here the Live column, and linkage cells, which keep their own link.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$recordUrlList],
        'methods' => ['makeRecordUrlList'],
        'sources' => ['resources/ui/reference/lists/options/record-url.yaml'],
    ])
</section>

<section class="ui-section" id="record-on-click">
    <h2>
        Record click and popups
    </h2>
    <p class="fg-3">
        <code>recordOnClick</code> runs JavaScript when a row is clicked, with <code>:id</code> replaced. The <code>popup</code> shorthand opens the controller's <code>onLoadPopupForm</code> handler in a popup, posting the id as <code>form_record_id</code>, and <code>popup@context</code> also posts <code>form_context</code>. Without <code>recordsPerPage</code> the list shows every record and no pages.
    </p>
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
    <h2>
        Rows by record
    </h2>
    <p class="fg-3">
        List events change single rows. <code>list.injectRowClass</code> returns classes for a row, here fading the archived posts, and <code>list.overrideRecordAction</code> returns a new <code>url</code> or <code>onclick</code>, or <code>clickable: false</code> to leave a row unlinked.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$rowEventsList],
        'methods' => ['makeRowEventsList'],
    ])
</section>

<section class="ui-section" id="rowlink">
    <h2>
        Row links on any table
    </h2>
    <p class="fg-3">
        The <code>rowlink</code> control, which lists use for their record links, works on any table. Each row follows the first link in it, whether the link goes to a page, opens a popup or runs an AJAX handler, and a cell with the <code>nolink</code> class is left out.
    </p>
    @include('reference._example', [
        'view' => 'reference.lists.examples.rowlink',
        'stacked' => true,
    ])
</section>

@endsection
