@extends('layouts.app')
@section('title', 'Data Table')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>datatable</code> field edits a list of rows in a spreadsheet grid. It saves the rows as a JSON list of objects keyed by column name, so the model attribute should be jsonable.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="default">
        <h2>
            Columns
        </h2>
        <p class="fg-3">
            Each column has a <code>title</code> and a <code>type</code>: string (the default), numeric, checkbox, dropdown, autocomplete, date or time. Checkboxes save 1 and 0. Insert Row and Delete Row act on the selected row, and an empty table starts with one blank row.
        </p>
        @include('reference._form_example', ['name' => 'default'])
    </section>

    <section class="ui-section" id="dates">
        <h2>
            Dates and times
        </h2>
        <p class="fg-3">
            Date and time cells open a picker. <code>dateFormat</code> (YYYY-MM-DD by default) and <code>timeFormat</code> (HH:mm by default) set both the shown and the saved text.
        </p>
        @include('reference._form_example', ['name' => 'dates'])
    </section>

    <section class="ui-section" id="dropdowns">
        <h2>
            Dropdowns
        </h2>
        <p class="fg-3">
            A <code>dropdown</code> column lists every option and saves the key of the one picked. An <code>autocomplete</code> column filters its options as you type and also accepts other text.
        </p>
        @include('reference._form_example', ['name' => 'dropdowns'])
    </section>

    <section class="ui-section" id="model-options">
        <h2>
            Options from the model
        </h2>
        <p class="fg-3">
            A dropdown or autocomplete column without <code>options</code> asks the model, through the field's <code>onGetDropdownOptions</code> handler, calling <code>getStockistsDataTableOptions($column, $rowData)</code> named after the field, or else <code>getDataTableOptions($field, $column, $rowData)</code>. <code>dependsOn</code> clears the city when the country changes and sends the row along, and the options of saved rows load with the field.
        </p>
        @include('reference._form_example', ['name' => 'model-options'])
    </section>

    <section class="ui-section" id="validation">
        <h2>
            Validation
        </h2>
        <p class="fg-3">
            A column's <code>validation</code> takes the rules required, integer, float, length and regex, each with an optional <code>message</code>. integer and float take <code>allowNegative</code>, <code>min</code> and <code>max</code>, and <code>requiredWith</code> applies a rule only when another column of the row has a value. Invalid cells are marked as you edit, and <code>requireValid</code> stops Save at the first one.
        </p>
        @include('reference._form_example', ['name' => 'validation'])
    </section>

    <section class="ui-section" id="fixed-rows">
        <h2>
            Fixed rows
        </h2>
        <p class="fg-3">
            <code>adding: false</code> and <code>deleting: false</code> remove the row buttons, shortcuts and menu items, which leaves no toolbar here. A column's <code>readOnly</code> locks its cells.
        </p>
        @include('reference._form_example', ['name' => 'fixed-rows'])
    </section>

    <section class="ui-section" id="no-toolbar">
        <h2>
            Without the toolbar
        </h2>
        <p class="fg-3">
            <code>toolbar: false</code> hides the toolbar, while rows can still be added and removed from the right click menu or with Alt+A, Alt+Shift+A and Alt+D.
        </p>
        @include('reference._form_example', ['name' => 'no-toolbar'])
    </section>

    <section class="ui-section" id="search-and-csv">
        <h2>
            Search, export and import
        </h2>
        <p class="fg-3">
            <code>searching</code> adds a search box that filters the rows. <code>csvExport</code> downloads the rows as CSV with the column titles as headers, through <code>onExportCsv</code>. <code>csvImport</code> reads a CSV file through <code>onImportCsv</code>, matching its header to the column titles or names, or else reading the columns in order.
        </p>
        @include('reference._form_example', ['name' => 'search-and-csv'])
    </section>

    <section class="ui-section" id="sorting">
        <h2>
            Sorting and moving
        </h2>
        <p class="fg-3">
            <code>sorting</code> sorts by a column when its header is clicked. <code>reorderRows</code> adds row numbers to drag rows by, and <code>reorderColumns</code> lets headers be dragged and frozen. <code>multiSelect: false</code> limits the selection to one cell.
        </p>
        @include('reference._form_example', ['name' => 'sorting'])
    </section>

    <section class="ui-section" id="height">
        <h2>
            Height
        </h2>
        <p class="fg-3">
            Without a <code>height</code> the table grows with its rows. A height in pixels keeps it that tall, with the rows scrolling inside.
        </p>
        @include('reference._form_example', ['name' => 'height'])
    </section>

    <section class="ui-section" id="empty-state">
        <h2>
            Empty state
        </h2>
        <p class="fg-3">
            <code>placeholder</code> shows a message when there are no rows, in place of the blank starting row.
        </p>
        @include('reference._form_example', ['name' => 'empty-state'])
    </section>

    <section class="ui-section" id="column-options">
        <h2>
            Column options
        </h2>
        <p class="fg-3">
            <code>width</code> takes pixels or a percentage of the table, and the other columns share what is left. <code>wrap</code> wraps long text over several lines, <code>ellipsis</code> cuts it short, and <code>readOnly</code> locks the column.
        </p>
        @include('reference._form_example', ['name' => 'column-options'])
    </section>

    <section class="ui-section" id="read-only">
        <h2>
            Read-only
        </h2>
        <p class="fg-3">
            <code>readOnly</code> shows the rows without editing, and keeps only search and export in the toolbar. The value still posts unchanged, where a <code>disabled</code> field posts nothing.
        </p>
        @include('reference._form_example', ['name' => 'read-only'])
    </section>

    <section class="ui-section" id="preview">
        <h2>
            Preview
        </h2>
        <p class="fg-3">
            In preview mode the grid shows the rows read-only, as it does for a read-only field. This example's form is made with <code>previewMode</code> on.
        </p>
        @include('reference._form_example', ['name' => 'preview'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
