---
subtitle: Edit a list of rows in a spreadsheet grid.
demo: /reference/form-widgets/data-table
---
# Data Table

The `datatable` field edits a list of rows in a spreadsheet grid, built on Hotsheet (the library's own
data grid). It saves the rows as a JSON list of objects keyed by column name, so cast the attribute with
`'array'` or `'json'` (or list it in `$jsonable` on an October Rain model). Rows with no values are left
out when saving.

## Columns {#default}

`columns` lists the columns by the key they save under. Each takes a `title` (the key when left out)
and a `type`:

- `string`, the default, plain text.
- `numeric`, a number.
- `checkbox`, saved as `1` or `0`.
- `dropdown` and `autocomplete`, see [Dropdowns](#dropdowns).
- `date` and `time`, see [Dates and times](#dates).

Insert Row adds a row at the end, and Delete Row removes the selected row (or the last one when none is
selected). An empty table starts with one blank row, and column edges can be dragged to resize them.

<<< @/../resources/ui/reference/form-widgets/data-table/default.yaml

The grid works like a spreadsheet from the keyboard. Arrow keys, `Tab` and `Enter` move between cells,
typing replaces a cell, `Enter` or `F2` edits it, `Escape` cancels an edit, and `Delete` clears the
selection. `Ctrl+C`, `Ctrl+X`, `Ctrl+V`, `Ctrl+Z` and `Ctrl+Y` copy, cut, paste, undo and redo, also
between the grid and a spreadsheet. `Alt+A` adds a row below the selected one, `Alt+Shift+A` above it,
and `Alt+D` deletes it. A right click opens a menu for inserting and removing rows.

To give new rows default values, listen for the `datatable:new-row` event, which fires for each row
added with the row object as `event.detail.row`, and set its keys.

## Dates and times {#dates}

Date and time cells open a picker. `dateFormat` (`YYYY-MM-DD` by default) and `timeFormat` (`HH:mm` by
default) set both the shown and the saved text, so `timeFormat: 'h:mm A'` shows and saves a twelve hour
time such as `2:30 PM`.

<<< @/../resources/ui/reference/form-widgets/data-table/dates.yaml

## Dropdowns {#dropdowns}

A `dropdown` column lists every option and must be one of them. It saves the key of the option picked
and shows its label. An `autocomplete` column narrows its options as you type and also accepts other
text: picking an option saves its key, and other text saves as typed. Both take `options` as a map of
keys to labels.

<<< @/../resources/ui/reference/form-widgets/data-table/dropdowns.yaml

## Options from the model {#model-options}

A dropdown or autocomplete column without `options` asks the model for them, through the field's
`onGetDropdownOptions` handler. The field calls a method named after it,
`get{FieldName}DataTableOptions($column, $rowData)`, or else
`getDataTableOptions($fieldName, $column, $rowData)`, which return the options as keys mapped to labels.
`$rowData` holds the row's current values.

`dependsOn` names another column (or a list of them) the options follow: changing the country clears
the city of that row and loads the cities again. So that saved values show their labels straight away,
the field also calls the method while rendering, once for each set of `dependsOn` values among the saved
rows, and the grid only asks over AJAX for values it hasn't seen.

<<< @/../resources/ui/reference/form-widgets/data-table/model-options.yaml

<<< @/../app/Models/Sample.php#docs-stockists-options{php}

## Validation {#validation}

A column's `validation` lists rules, checked in order, and each takes an optional `message`:

- `required`, the cell must have a value.
- `integer` and `float`, whole or decimal numbers, positive unless `allowNegative` is set, with `min`
  and `max`.
- `length`, the text length, with `min` and `max`.
- `regex`, the text must match `pattern`, with optional `modifiers`.

`min` and `max` each take `value` and an optional `message`. Every rule also takes `requiredWith`, a
column name that applies the rule only when that column of the row has a value. Empty cells only fail
`required`.

Invalid cells are marked as you edit. With `requireValid`, saving stops at the first invalid cell of a
row that has values, which is selected and its message flashed. A request counts as saving when its
handler is the form's own `data-request` (here `onSaveExamples`) or is named `onSave`.

The rules run in the browser, so validate the saved rows on the server as well when they matter.

<<< @/../resources/ui/reference/form-widgets/data-table/validation.yaml

## Fixed rows {#fixed-rows}

`adding: false` and `deleting: false` remove the row buttons, shortcuts and menu items. With nothing
left to show, the toolbar is left out too. A column's `readOnly` locks its cells.

<<< @/../resources/ui/reference/form-widgets/data-table/fixed-rows.yaml

## Without the toolbar {#no-toolbar}

`toolbar: false` hides the toolbar, while rows can still be added and removed from the right click menu
or with `Alt+A`, `Alt+Shift+A` and `Alt+D`.

<<< @/../resources/ui/reference/form-widgets/data-table/no-toolbar.yaml

## Search, export and import {#search-and-csv}

`searching` adds a search box that highlights the cells containing the text.

`csvExport` adds an Export button that downloads the rows as a CSV file named after the field, through
the field's `onExportCsv` handler. The header row holds the column titles, and values a spreadsheet would
read as a formula are prefixed with an apostrophe.

`csvImport` adds an Import button that reads a CSV file through `onImportCsv` and appends its rows. The
file may be separated by commas, semicolons or tabs. A header row is matched to the column titles or
names, and without one the values are read in column order.

<<< @/../resources/ui/reference/form-widgets/data-table/search-and-csv.yaml

## Sorting and moving {#sorting}

`sorting` sorts by a column when its header is clicked, for viewing only: the rows save in their own
order. `reorderRows` adds row numbers to drag rows by, which does change the saved order: dragging a row
while sorted saves the order you see and clears the sort. `reorderColumns` lets headers be dragged and frozen, for viewing. `multiSelect: false` limits the
selection to one cell at a time.

<<< @/../resources/ui/reference/form-widgets/data-table/sorting.yaml

## Height {#height}

Without a `height`, the table grows with its rows. A height in pixels keeps it that tall, with the rows
scrolling inside.

<<< @/../resources/ui/reference/form-widgets/data-table/height.yaml

## Empty state {#empty-state}

`placeholder` shows a message while there are no rows, in place of the blank starting row.

<<< @/../resources/ui/reference/form-widgets/data-table/empty-state.yaml

## Column options {#column-options}

`width` takes pixels (`160px`) or a percentage of the table (`30%`), and columns without one share the
space left over. Long text is cut short with an ellipsis, unless the column sets `wrap` to wrap it over
several lines. `ellipsis: true` states the cut explicitly. `readOnly` locks the column.

<<< @/../resources/ui/reference/form-widgets/data-table/column-options.yaml

## Read-only {#read-only}

A `readOnly` field shows the rows without editing them: they can be selected, copied, searched, sorted
and exported, and the toolbar drops Insert Row, Delete Row and Import. The value still posts unchanged,
where a `disabled` field, shown the same way, posts nothing.

<<< @/../resources/ui/reference/form-widgets/data-table/read-only.yaml

## Preview {#preview}

In preview mode the grid shows the rows read-only, as it does for a read-only field. This example's
form is made with `previewMode` on.

<<< @/../resources/ui/reference/form-widgets/data-table/preview.yaml

## Options

Option | Description
------------- | -------------
**columns** | the columns, keyed by the name each saves under, see [Column options](#column-option-list).
**adding** | allows adding rows. Default: `true`.
**deleting** | allows removing rows. Default: `true`.
**toolbar** | shows the toolbar, when it has a button or the search box to show. Default: `true`.
**searching** | adds a search box that highlights matching cells. Default: `false`.
**csvExport** | adds an Export button that downloads the rows as CSV. Default: `false`.
**csvImport** | adds an Import button that appends the rows of a CSV file. Default: `false`.
**sorting** | sorts the view by a column when its header is clicked. Default: `false`.
**reorderRows** | adds row numbers to drag rows into a new order. Default: `false`.
**reorderColumns** | lets column headers be dragged and frozen. Default: `false`.
**multiSelect** | allows selecting a range of cells. Default: `true`.
**height** | the table height in pixels, with the rows scrolling inside. Default: grows with the rows.
**placeholder** | a message shown while there are no rows. Default: one blank row instead.
**requireValid** | stops a save while a row with values has an invalid cell. Default: `false`.

The field also takes the common [field properties](/forms/fields#field-properties), such as `readOnly`
and `disabled`.

### Column options {#column-option-list}

Option | Description
------------- | -------------
**title** | the header text. Default: the column name.
**type** | `string`, `numeric`, `checkbox`, `dropdown`, `autocomplete`, `date` or `time`. Default: `string`.
**options** | the options of a `dropdown` or `autocomplete` column, as keys mapped to labels. Default: asks the model.
**dependsOn** | another column, or a list of them, whose changes clear this cell and reload its model options.
**width** | the width in pixels (`100px`) or a percentage of the table (`20%`). Default: a share of the space left.
**readOnly** | locks the column. Default: `false`.
**wrap** | wraps long text over several lines. Default: `false`.
**ellipsis** | cuts long text short with an ellipsis, as columns without `wrap` do. Default: `false`.
**dateFormat** | the shown and saved format of a `date` column. Default: `YYYY-MM-DD`.
**timeFormat** | the shown and saved format of a `time` column. Default: `HH:mm`.
**validation** | the rules checked in the browser, see [Validation](#validation).
