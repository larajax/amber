---
subtitle: Sort, page and link a list, check its rows and choose its columns.
demo: /reference/lists/options
---
# List Options

The options of `Lists::make()` decide how a list sorts, pages and links its rows, and whether visitors
can check rows or choose the columns. Each example shows the method that builds the list and the YAML
of its columns. Every list here except one sets `customPageName: false`, so the lists on the page do
not share a page number. Adding a list to a page is covered in [Building a List Page](/guides/lists).

## Sorting {#sorting}

Clicking a header sorts the list by that column, a second click reverses the order, and the list
remembers the choice for the session. `defaultSort` sets the order until then, as a column name, which
sorts ascending, or as an array with a `column` and a `direction`. A list without `defaultSort` uses the
column marked `sortableDefault`, if there is one.

A column with `sortable: false` has a plain header, like the excerpt here. `showSorting: false` removes
the sort links from every header, and the default order still applies.

<<< @/../app/Controllers/Reference/ListsController/OptionsExamples.php#docs-sorting{php}

<<< @/../resources/ui/reference/lists/options/sorting.yaml

## Pagination {#pagination}

`recordsPerPage` splits the list into pages, with the record count and a link for each page. The dots
between the links open a box to type a page number. A list without `recordsPerPage` shows every record
on one page.

`customPageName` names the page in the address, here `?postsPage=2`, so a reload or a shared link
opens the same page. It is `page` by default, which suits a page with one list. `false` keeps the page
out of the address and posts it with each request instead, so lists on the same page each keep their
own. A new search or filter goes back to the first page.

<<< @/../app/Controllers/Reference/ListsController/OptionsExamples.php#docs-pagination{php}

<<< @/../resources/ui/reference/lists/options/posts.yaml

## Simple pagination {#simple-pagination}

`showPageNumbers: false` leaves out the record count and the page links, keeping only first, previous
and next, so the list never counts its records. It suits large tables, where counting is slow.

`showPagination` turns the pager off altogether. It is `auto` by default, which shows the pager
whenever `recordsPerPage` is set, and `false` shows every record.

<<< @/../app/Controllers/Reference/ListsController/OptionsExamples.php#docs-simple-pagination{php}

## List setup {#setup}

`showSetup` lets the visitor choose the columns, their order and the page size, and the list keeps the
choice. The setup opens from the icon a [toolbar](/lists/toolbar#search) adds with `setupHandler`,
given the list's own `onLoadSetup` handler through `getEventHandler()`.

In the setup, drag the columns into a new order, check the ones to show and pick a page size from
`perPageOptions`, which is 20, 40, 80, 100 and 120 by default, with `recordsPerPage` added when it is
missing. Reset to Default forgets the choice. Columns marked `invisible` start unchecked, while a
`hidden` column is never shown, not even in the setup.

The choice is kept in the session. To keep it for good, such as per user in your database, extend the
widget and override `getPreferenceStorage()` to return your own store: an object with
`get($key, $default)`, `set($key, $value)` and `reset($key)` methods.

<<< @/../app/Controllers/Reference/ListsController/OptionsExamples.php#docs-setup{php}

<<< @/../resources/ui/reference/lists/options/setup.yaml

## Checkboxes {#checkboxes}

`showCheckboxes` adds a checkbox to each row, and one in the header that checks or clears them all.
Hold Shift to check a range of rows. Rows stay checked when the list moves to another page and back.

A toolbar bound to the list with `bindToListWidget()` acts on the checked rows. Its button with
`checked: true` stays disabled until a row is checked, then sends the ids of the rows checked on the
page to its handler as `checked`. [Checked-state buttons](/lists/toolbar#checked-state) covers the same
for buttons of your own markup.

<<< @/../app/Controllers/Reference/ListsController/OptionsExamples.php#docs-checkboxes{php}

<<< @/../app/Controllers/Reference/ListsController/OptionsExamples.php#docs-checked-posts{php}

## Searchable columns and no records {#searchable}

A search box bound to the list looks in the columns marked `searchable`, including a column read from
a relation and the `invisible` excerpt, which is searched while it is hidden. Search for "nested" to
match a title, "releases" to match a category, or a word that no post has to see `noRecordsMessage`,
which is *No records found.* by default. [Search](/lists/search) covers the search box itself.

<<< @/../app/Controllers/Reference/ListsController/OptionsExamples.php#docs-searchable{php}

<<< @/../resources/ui/reference/lists/options/searchable.yaml

## Record URL {#record-url}

`recordUrl` links every row to an address, where `:id` and any other `:attribute` take the record's
values. The path becomes an address through [`Ui::url()`](/extending/seams#widget-paths). The whole row
follows the link, except cells with `clickable: false`, here the Live column, and linkage cells, which
keep their own link. Ctrl or Cmd and click, or a middle click, opens the row in a new tab.

<<< @/../app/Controllers/Reference/ListsController/OptionsExamples.php#docs-record-url{php}

<<< @/../resources/ui/reference/lists/options/record-url.yaml

## Record click and popups {#record-on-click}

`recordOnClick` runs JavaScript when a row is clicked, with `:id` and other attributes replaced, and
takes the place of `recordUrl`. The `popup` shorthand opens the controller's `onLoadPopupForm` handler
in a popup, posting the record id as `form_record_id`, and `popup@context` also posts the context as
`form_context`. The handler returns the popup's markup: a header, a body and a footer, often around a
form. A handler in the popup that saves the record can return the list's `onRefresh()` to update the
rows.

The shorthand calls `jax.listOnLoadForm()`, which the browser bundle provides. A Vite app that imports
`jax` should also assign it to `window.jax`.

This list sets no `recordsPerPage`, so it shows every category on one page.

<<< @/../app/Controllers/Reference/ListsController/OptionsExamples.php#docs-popup{php}

<<< @/../app/Controllers/Reference/ListsController/OptionsExamples.php#docs-popup-form{php}

<<< @/../resources/ui/reference/lists/options/popup.yaml

<<< @/../resources/views/reference/lists/options/_category_popup.blade.php{blade}

## Rows by record {#row-events}

List events change single rows. Bind a listener with `bindEvent()` on the list, as here, or override
the matching method in a subclass of the widget, such as `eventInjectRowClass()`.

- `list.injectRowClass` receives the record and returns classes for its row, here fading the archived
  posts.
- `list.overrideRecordAction` receives the record, its address and its onclick script, and returns a
  new address as a string, or an array with a new `url` or `onclick`, or `clickable: false` to leave
  the row unlinked.

<<< @/../app/Controllers/Reference/ListsController/OptionsExamples.php#docs-row-events{php}

## Row links on any table {#rowlink}

The `rowlink` control, which lists use for their record links, works on any table with
`data-control="rowlink"`. Each row follows the first link inside it, whether the link goes to a page,
opens a popup or runs an AJAX request, and a cell or link with the `nolink` class is left out. A linked
row can also be reached with Tab and followed with Space.

`data-target` picks the links by another selector than `a`, `data-exclude-class` names another class
in place of `nolink`, and `data-linked-class` sets the class added to each linked row, `rowlink` by
default.

<<< @/../resources/views/reference/lists/examples/rowlink.blade.php{blade}

<<< @/../app/Controllers/Reference/ListsController/OptionsExamples.php#docs-rowlink-handlers{php}

## Options {#options}

`Lists::make()` takes these options as named arguments.

Option | Description
------------- | -------------
**model** | the model whose records the list shows.
**columns** | the column definitions, as a YAML file path or an array. See [Columns](/lists/columns).
**alias** | the widget's name, unique on the page, which prefixes its handlers, as in `sortingList::onSort`. Default: `list`.
**recordsPerPage** | the rows on each page, which turns pagination on. Default: every record on one page.
**showPagination** | shows the pager. `auto` shows it when `recordsPerPage` is set. Default: `auto`.
**showPageNumbers** | shows the record count and a link for each page. `false` keeps first, previous and next, and skips the count. Default: `true`.
**customPageName** | the query parameter for the page number. `false` keeps the page out of the address. Default: `page`.
**defaultSort** | the starting order, as a column name or an array with `column` and `direction`.
**showSorting** | lets the visitor sort by clicking a header. Default: `true`.
**showCheckboxes** | adds a checkbox to each row. Default: `false`.
**showSetup** | lets the visitor choose the columns, their order and the page size. Default: `false`.
**perPageOptions** | the page sizes offered in the list setup. Default: `[20, 40, 80, 100, 120]`.
**recordUrl** | the address each row links to, with `:id` and other attributes replaced.
**recordOnClick** | JavaScript run when a row is clicked, with the same replacements, or `popup` and `popup@context` to open the record in a popup.
**noRecordsMessage** | shown when the list has no records. Default: `No records found.`
**customViewPath** | a view path searched first for the list's partials, so files of your own replace them.
**pivotMode** | reads the row id and the `:attribute` replacements from each record's pivot, for a list of related records. Default: `false`.
