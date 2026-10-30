@extends('layouts.app')
@section('title', 'Data Table')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>datatable</code> field edits rows in a spreadsheet grid and saves them as a JSON list.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="default">
        @include('reference._heading', ['id' => 'default', 'title' => 'Columns', 'caption' => 'String, <code>numeric</code> and <code>checkbox</code> columns with a <code>title</code> each.'])
        @include('reference._form_example', ['name' => 'default'])
    </section>

    <section class="ui-section" id="dates">
        @include('reference._heading', ['id' => 'dates', 'title' => 'Dates and times', 'caption' => '<code>date</code> and <code>time</code> columns, one with a <code>timeFormat</code>.'])
        @include('reference._form_example', ['name' => 'dates'])
    </section>

    <section class="ui-section" id="dropdowns">
        @include('reference._heading', ['id' => 'dropdowns', 'title' => 'Dropdowns', 'caption' => 'A <code>dropdown</code> and an <code>autocomplete</code> column with <code>options</code>.'])
        @include('reference._form_example', ['name' => 'dropdowns'])
    </section>

    <section class="ui-section" id="model-options">
        @include('reference._heading', ['id' => 'model-options', 'title' => 'Options from the model', 'caption' => '<code>getStockistsDataTableOptions()</code> with a city that <code>dependsOn</code> the country.'])
        @include('reference._form_example', ['name' => 'model-options'])
    </section>

    <section class="ui-section" id="validation">
        @include('reference._heading', ['id' => 'validation', 'title' => 'Validation', 'caption' => 'Column <code>validation</code> rules with <code>requireValid</code> on.'])
        @include('reference._form_example', ['name' => 'validation'])
    </section>

    <section class="ui-section" id="fixed-rows">
        @include('reference._heading', ['id' => 'fixed-rows', 'title' => 'Fixed rows', 'caption' => '<code>adding: false</code>, <code>deleting: false</code> and a <code>readOnly</code> column.'])
        @include('reference._form_example', ['name' => 'fixed-rows'])
    </section>

    <section class="ui-section" id="no-toolbar">
        @include('reference._heading', ['id' => 'no-toolbar', 'title' => 'Without the toolbar', 'caption' => '<code>toolbar: false</code>, leaving the menu and shortcuts.'])
        @include('reference._form_example', ['name' => 'no-toolbar'])
    </section>

    <section class="ui-section" id="search-and-csv">
        @include('reference._heading', ['id' => 'search-and-csv', 'title' => 'Search, export and import', 'caption' => '<code>searching</code>, <code>csvExport</code> and <code>csvImport</code>.'])
        @include('reference._form_example', ['name' => 'search-and-csv'])
    </section>

    <section class="ui-section" id="sorting">
        @include('reference._heading', ['id' => 'sorting', 'title' => 'Sorting and moving', 'caption' => '<code>sorting</code>, <code>reorderRows</code>, <code>reorderColumns</code> and <code>multiSelect: false</code>.'])
        @include('reference._form_example', ['name' => 'sorting'])
    </section>

    <section class="ui-section" id="height">
        @include('reference._heading', ['id' => 'height', 'title' => 'Height', 'caption' => '<code>height: 220</code> with the rows scrolling inside.'])
        @include('reference._form_example', ['name' => 'height'])
    </section>

    <section class="ui-section" id="empty-state">
        @include('reference._heading', ['id' => 'empty-state', 'title' => 'Empty state', 'caption' => 'A <code>placeholder</code> message in place of the blank row.'])
        @include('reference._form_example', ['name' => 'empty-state'])
    </section>

    <section class="ui-section" id="column-options">
        @include('reference._heading', ['id' => 'column-options', 'title' => 'Column options', 'caption' => 'Column <code>width</code>, <code>wrap</code>, <code>ellipsis</code> and <code>readOnly</code>.'])
        @include('reference._form_example', ['name' => 'column-options'])
    </section>

    <section class="ui-section" id="read-only">
        @include('reference._heading', ['id' => 'read-only', 'title' => 'Read-only', 'caption' => '<code>readOnly</code> rows that can still be searched and exported.'])
        @include('reference._form_example', ['name' => 'read-only'])
    </section>

    <section class="ui-section" id="preview">
        @include('reference._heading', ['id' => 'preview', 'title' => 'Preview', 'caption' => 'Read-only rows in a <code>previewMode</code> form.'])
        @include('reference._form_example', ['name' => 'preview'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
