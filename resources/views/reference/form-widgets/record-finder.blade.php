@extends('layouts.app')
@section('title', 'Record Finder')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>recordfinder</code> field picks one record from a searchable list in a popup, which suits relations with too many records for a dropdown. Clicking a row picks it, and the field shows its name and description.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="default">
        <h2>
            Picking a record
        </h2>
        <p class="fg-3">
            <code>list</code> defines the popup columns, and searchable columns feed the search box. <code>nameFrom</code> and <code>descriptionFrom</code> describe the picked record, <code>title</code> heads the popup, and <code>recordsPerPage</code> sets the page size.
        </p>
        @include('reference._form_example', ['name' => 'default'])
    </section>

    <section class="ui-section" id="scopes">
        <h2>
            Scopes
        </h2>
        <p class="fg-3">
            <code>scope</code> narrows the list with a model scope or a static method, here verified users only. <code>searchScope</code> replaces the search with a scope of its own, which matches the start of a name or email.
        </p>
        @include('reference._form_example', ['name' => 'scopes'])
    </section>

    <section class="ui-section" id="popup">
        <h2>
            The popup list
        </h2>
        <p class="fg-3">
            <code>filter</code> adds filter scopes above the list, <code>defaultSort</code> orders it, <code>showSetup</code> adds the list setup button, and <code>popupSize</code> sets the popup width.
        </p>
        @include('reference._form_example', ['name' => 'popup'])
    </section>

    <section class="ui-section" id="tree">
        <h2>
            Trees and the stacked layout
        </h2>
        <p class="fg-3">
            When the related model is a nested tree, the popup lists the records as a tree. <code>inlineOptions: false</code> stacks the picked record above its buttons, which leaves room for a longer description.
        </p>
        @include('reference._form_example', ['name' => 'tree'])
    </section>

    <section class="ui-section" id="without-relation">
        <h2>
            Without a relation
        </h2>
        <p class="fg-3">
            <code>useRelation: false</code> lists the <code>modelClass</code> records and saves a plain value, read from the <code>keyFrom</code> column. <code>conditions</code> filters the list with a raw where clause.
        </p>
        @include('reference._form_example', ['name' => 'without-relation'])
    </section>

    <section class="ui-section" id="read-only">
        <h2>
            Read-only
        </h2>
        <p class="fg-3">
            <code>readOnly</code> shows the record without its buttons and keeps its key in a hidden input. This copy reads the reviewer through <code>valueFrom</code>, and its underscore name keeps it out of the save.
        </p>
        @include('reference._form_example', ['name' => 'read-only'])
    </section>

    <section class="ui-section" id="preview">
        <h2>
            Preview
        </h2>
        <p class="fg-3">
            <code>disabled</code> shows the record in preview mode and leaves the field out of the save. With no record it says so.
        </p>
        @include('reference._form_example', ['name' => 'preview'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
