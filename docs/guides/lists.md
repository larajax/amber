---
subtitle: Bind a list, a filter and a toolbar into one page, then edit its rows in popups.
---
# Building a List Page

This guide builds the index pages of the demo app's Showcase. The Posts page binds a list, a filter and
a toolbar together, with bulk actions on the checked rows and a quick post popup. The Groups page edits
its rows in a popup, and the Categories page shows a tree that you can reorder by dragging. Every
snippet is the demo's own code, so you can [run the demo app](/#run-the-demo-app) and
try each page as you read.

## Rendering a list {#rendering-a-list}

A list starts with a YAML file of columns. The posts list has a column for each attribute it shows,
several of them related records:

<<< @/../resources/ui/post/columns.yaml

Each key names an attribute, and its definition sets the header, the column type and what the column
allows. `sortable` adds a sorting link to the header, `searchable` includes the column in a search, and
`invisible` hides the column until someone shows it from the list setup. The
[columns page](/lists/columns) covers every column type.

The posts controller's `index` action returns `view('posts.index', $this->prepareListVars())`, and
the `PostList` trait builds the list with `Lists::make()`:

<<< @/../app/Controllers/PostsController/PostList.php#docs-make-list{php}

`model` is an empty instance of the model to list, and `columns` the YAML file (a path starting with
`~/` resolves from the application root, and an array of definitions works too). The other options set
the page size and the sizes offered in the list setup, add checkboxes and the setup popup, link each
row to its post, and sort the newest posts first. Every option is listed under
[Widget configuration](#widget-configuration), and the [list options](/lists/options) page shows each
one live.

The view prints the widgets. Widgets are `Htmlable`, so Blade's `{{ }}` renders their markup as it is:

<<< @/../resources/views/posts/index.blade.php{blade}

Sorting, paging, searching and filtering post back to the page's own address, where the list's
handlers (`list::onSort`, `list::onPaginate` and so on) answer them. The route must accept POST as well
as GET, as shown in [Routes](/installation#routes).

## Binding a toolbar and a filter {#binding}

`prepareListVars()` builds the three widgets of the page and binds the filter and the toolbar to the
list:

<<< @/../app/Controllers/PostsController/PostList.php#docs-prepare-list{php}

- `$filter->bindToListWidget($list)` adds the filter's scopes to the list's query and shares the
  list's page parameter. When a scope changes, the filter returns the list refreshed from page one in
  the same response.
- `$toolbar->bindToListWidget($list)` links the toolbar's buttons to the list's checkboxes, passes the
  search box's `mode` and `scope` to the list, applies the current search term, and refreshes the list
  when a new term is entered.

The list is built first, since the others bind to it, and kept in the public `$listWidget` property so
the bulk action and popup handlers can refresh it. The demo's top bar has a search box of its own that
submits to this page as `?q=`, and `setActiveTerm()` hands that term to the toolbar's search before the
binding applies it.

The filter and the toolbar are built in the same trait:

<<< @/../app/Controllers/PostsController/PostList.php#docs-filter-toolbar{php}

`Filter::make()` takes the model and a YAML file of scopes. Each scope constrains the list's query, and
the values persist in the session, so the filter survives a reload. The posts filter has a switch, group
scopes for the category, author, tags and status, a checkbox that hides archived posts by default, and a
date range:

<<< @/../resources/ui/post/scopes.yaml

The [scope types](/filters/scopes) and [filter options](/filters/options) pages cover every scope.

`Toolbar::make()` takes the buttons, the search box and the list setup icon:

- `buttons` is a YAML file of button definitions, or a partial of `Ui` buttons as here. Either renders
  inside the toolbar, linked to the list.
- `search` adds the search box. `prompt` is its placeholder, and `scope` names a model scope that runs
  the search instead of the `searchable` columns, here `applySearch`, which also matches the category,
  tag and author names (see [search with a scope](/lists/search#scope)).
- `setupHandler` shows the icon that opens the list setup. `$list->getEventHandler('onLoadSetup')`
  returns `list::onLoadSetup`, the list's own handler.

The posts toolbar is a partial, which lets it hold a popup button and a dropdown:

<<< @/../resources/views/posts/_list_toolbar.php

The Bulk Actions dropdown is disabled until a row is checked (`dataListCheckedTrigger`), and each item
sends the ids of the checked rows with its request (`dataListCheckedRequest`). The Authors page declares
its buttons in YAML instead, where `checked: true` does both for its Delete button:

<<< @/../resources/ui/user/buttons.yaml

See [buttons from YAML](/lists/toolbar#yaml-buttons) and [buttons from a partial](/lists/toolbar#partial)
for the full set of button options.

## Bulk actions on checked rows {#bulk-actions}

The bulk action handlers read the checked ids, change those posts, and return the refreshed list with a
flash message:

<<< @/../app/Controllers/PostsController/PostList.php#docs-bulk-actions{php}

The checked ids arrive as `checked`. `$this->listWidget->getAllCheckedIds()` reads them too, adding rows
checked on other pages of the list. `$this->listWidget->onRefresh()` returns the list's markup keyed by
its element id, which `ajax()->update()` sends to the browser. The property holds the list because
Larajax runs the `index` action before the handler, so `prepareListVars()` has already built it.

## Record URLs {#record-urls}

`recordUrl: 'posts/:id/edit'` makes each row a link to its post. `:id`, and any other attribute name
after a colon, is replaced with the record's value, and the result becomes an address through
`Ui::url()`, which you can [change for your application](/extending/seams#ui-url). Clicking anywhere on a
row follows the link, except cells that link on their own. The author and category columns are
`linkage` columns with their own `linkUrl`, so they open the author or the category instead.

`recordOnClick` runs JavaScript on a row click in place of a link, with the same `:id` replacement. Its
`popup` value opens the record in a popup, as the Groups page does next. See
[record URL](/lists/options#record-url) and [record click](/lists/options#record-on-click).

## Editing rows in a popup {#popup-editing}

The Groups page edits a group in a popup when you click its row. The list sets `recordOnClick: 'popup'`:

<<< @/../app/Controllers/GroupsController/GroupList.php

A row click calls the controller's `onLoadPopupForm` handler, posting the record's id as
`form_record_id` (and `form_context` with `popup@context`). The handler returns the popup's markup, and
the popup's form posts back to `onSavePopupForm`:

<<< @/../app/Controllers/GroupsController/GroupForm.php#docs-popup-form{php}

Both handlers find the group by the posted id and build the same form. The popup's markup is a form
with a dialog header, body and footer around the form widget:

<<< @/../resources/views/groups/_popup_form.blade.php{blade}

The hidden `form_record_id` input sends the id with the save. `data-popup-load-indicator` shows a
loading state while the request runs and closes the popup once it succeeds, and the handler's
`$this->listWidget->onRefresh()` updates the row behind it. The `popup` shorthand calls the global
`jax` object that the Larajax bundle provides, so an application that imports `jax` through Vite should
also assign it to `window.jax`. The [popups page](/ui/popups) covers the dialog markup and the other ways
to open a popup.

## A quick create popup {#quick-create}

The Posts toolbar's Quick Post button, or Shift+Q, opens a short form for a new post without leaving
the list. `Ui::popupButton()` in the toolbar partial above names the handler that renders the popup,
and the `QuickPostForm` trait holds it:

<<< @/../app/Controllers/PostsController/QuickPostForm.php

The quick form has its own fields:

<<< @/../resources/ui/post/fields_quick.yaml

and its own popup markup, posting to `onCreateQuickPost`:

<<< @/../resources/views/posts/_quick_post_form.blade.php{blade}

The form is built with the alias `quickPostForm`, so its inputs and handlers stay apart from those of
the main post form, which uses the default alias `form`. Creating the post returns the refreshed list,
so the new post appears behind the closing popup. The post model's rules validate the quick form the
same way as the full one, see [validation](/guides/forms#validation).

## A tree of records {#list-structure}

The Categories page shows the categories as a nested tree. `ListStructure` is a list that adds the tree
and drag-and-drop reordering, and it takes every list option as well as its own:

<<< @/../app/Controllers/CategoriesController/CategoryList.php

`showTree` draws the tree with arrows to open and close each branch, `treeExpanded` starts with every
branch open, and `maxDepth` stops a category from nesting more than three levels deep. With
`dragRow: false`, a row moves by its handle only, so a click on the row still opens the category. The
category is an October Rain model with the `NestedTree` trait, which stores the tree and the order, and
dragging a row moves the category in place. Searching shows the matches as a flat list until the search
is cleared, and so does sorting by a column. The [list structure](/lists/list-structure) page shows a
browsing tree and a flat reorderable list as well.

## Widget configuration {#widget-configuration}

`Lists::make()` takes these options as named arguments:

Option | Description
------------- | -------------
**model** | an instance of the model to list.
**columns** | the column definitions, as a YAML file path or an array.
**recordsPerPage** | the number of rows on a page. Pagination turns on when it is set.
**perPageOptions** | the page sizes offered in the list setup. Default: `[20, 40, 80, 100, 120]`.
**showCheckboxes** | adds a checkbox to each row and one in the header. Default: `false`.
**showSetup** | lets visitors choose the columns and the page size in a setup popup, kept in the session. Default: `false`.
**recordUrl** | the link for each row, where `:id` and other `:attribute` names take the record's values.
**recordOnClick** | JavaScript to run on a row click, with the same replacements. `popup` (or `popup@context`) opens the controller's `onLoadPopupForm` handler in a popup.
**defaultSort** | the initial sort, as a column name or `['column' => 'name', 'direction' => 'desc']`.
**showSorting** | adds sorting links to the column headers. Default: `true`.
**noRecordsMessage** | the message shown when no records match. Default: `No records found.`
**showPagination** | shows the pagination. Default: `auto`, on when `recordsPerPage` is set.
**showPageNumbers** | shows the record count and a link for each page. Turn it off to skip counting the records on a large table. Default: `true`.
**customPageName** | the query string name of the page number. Default: `page`.
**expandLastColumn** | lets the last column take the spare width. Default: `false`.
**alias** | the name the list's AJAX handlers are addressed by, as `alias::onHandler`. Default: `list`. Give each list on a page its own.

`ListStructure::make()` adds `showTree`, `treeExpanded`, `showReorder`, `maxDepth` and `dragRow`, see
the [list structure](/lists/list-structure) page. `Filter::make()` takes `model`, `scopes`, `context`
and `alias`, and `Toolbar::make()` takes `buttons`, `search`, `setupHandler` and `alias`. Options
without a named argument pass through `make()` by name, and `Lists::makeWithConfig($config)` takes a
configuration array put together at runtime.

## Extending the list {#extending-the-list}

To constrain the query without subclassing, add a callback with `addFilter()`. It receives the query
before the list reads its records, as in `$list->addFilter(fn ($query) => $query->where('published', true))`.
A bound filter adds its scopes the same way.

Every extension point of the list is a method that fires a local event. Bind to the event after
building the list, such as `$list->bindEvent('list.injectRowClass', function ($record) { ... })` to add a
class to some rows (see [rows by record](/lists/options#row-events)), or subclass
`Larajax\Ui\Widgets\Lists`, override the method, and build your class with `make()` as before. Where
a point may return something, a listener's return value is used the same way. The extension points
are:

Method | Event | Called
------------- | ------------- | -------------
**eventExtendColumns()** | `list.extendColumns` | once the columns are defined, to add them with `addColumns()` or remove them with `removeColumn()`.
**eventExtendQueryBefore($query)** | `list.extendQueryBefore` | before the list applies its own constraints to the query.
**eventExtendSearchQuery($query)** | `list.extendSearchQuery` | inside the search conditions, to search more places.
**eventExtendSortColumn($query, $column, $direction)** | `list.extendSortColumn` | after the sort is applied.
**eventExtendQuery($query)** | `list.extendQuery` | after the list's constraints, and may return a replacement query.
**eventExtendRecords($records)** | `list.extendRecords` | once the records are read, and may return a replacement collection.
**eventRefresh($result)** | `list.refresh` | after a refresh, with the partial updates. Return it with your own updates added.
**eventOverrideRecordAction($record, $url, $onClick)** | `list.overrideRecordAction` | for each row, and may return a new URL, or an array with `url`, `onclick` or `clickable`.
**eventOverrideHeaderValue($column, $value)** | `list.overrideHeaderValue` | for each header, and may return a new label.
**eventOverrideColumnValueRaw($record, $column, $value)** | `list.overrideColumnValueRaw` | for each cell before its column type formats it, and may return a new value.
**eventOverrideColumnValue($record, $column, $value)** | `list.overrideColumnValue` | for each cell after formatting, and may return new markup.
**eventInjectRowClass($record, $value)** | `list.injectRowClass` | for each row, and may return CSS classes for it.

A list built without `columns` asks the model for them instead: a `defineListColumns($list)` method on
the model adds each column with `$list->defineColumn('name', 'Label')`, which returns a definition to
chain the column's options on, such as `->sortable(true)`.

## Extending the filter {#extending-the-filter}

### Model hooks {#filter-model-hooks}

The filter's model can take part in two ways:

- **`filterScopes($scopes, $context)`** runs every time the filter renders and after each scope
  change, with each scope reachable by name on `$scopes`. Change a scope's properties there, such as
  `$scopes->category->hidden = true`, to adapt the filter to the record or the visitor. `$context` is
  the filter's `context` option.
- **`defineFilterScopes($filter)`** supplies the scopes when the filter is built without a `scopes`
  option. Add each one with `$filter->defineScope('name', 'Label')`, which returns a definition to chain
  the scope's options on, such as `->displayAs('checkbox')->conditions('is_active = 1')`. The scopes
  then work exactly as if they came from YAML.

### Subclassing the filter {#subclassing-the-filter}

For changes that belong to the filter rather than the model, subclass `Larajax\Ui\Widgets\Filter`,
override these methods, and build your class with `make()` as before:

Method | Called
------------- | -------------
**eventExtendScopesBefore()** | before the scope definitions are read.
**eventExtendScopes()** | once the scopes are defined, to add them with `addScopes()` or remove them with `removeScope()`.
**eventOverrideHeaderValue($scope, $value)** | for each scope, and may return a new label for its button.
**eventUpdate($result, $params)** | after a scope changes, with the partial updates. The base class adds the bound list here, so call `parent::eventUpdate()` and add your own updates.
**eventExtendQuery($query, $scope)** | when a scope reads its options, to constrain the option query.

## Next steps

- [Building a Form Page](/guides/forms): the post form each row links to
- [Columns](/lists/columns): every column type
- [Scope Types](/filters/scopes): every filter scope
