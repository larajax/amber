@extends('layouts.app')
@section('title', 'Record Finder')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>recordfinder</code> field picks one record from a searchable list in a popup.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="default">
        @include('reference._heading', ['id' => 'default', 'title' => 'Picking a record', 'caption' => '<code>list</code>, <code>nameFrom</code>, <code>descriptionFrom</code>, <code>title</code> and <code>recordsPerPage</code>.'])
        @include('reference._form_example', ['name' => 'default'])
    </section>

    <section class="ui-section" id="scopes">
        @include('reference._heading', ['id' => 'scopes', 'title' => 'Scopes', 'caption' => '<code>scope</code> naming a static method, and a <code>searchScope</code>.'])
        @include('reference._form_example', ['name' => 'scopes'])
    </section>

    <section class="ui-section" id="popup">
        @include('reference._heading', ['id' => 'popup', 'title' => 'The popup list', 'caption' => '<code>filter</code>, <code>defaultSort</code>, <code>showSetup</code> and <code>popupSize</code>.'])
        @include('reference._form_example', ['name' => 'popup'])
    </section>

    <section class="ui-section" id="tree">
        @include('reference._heading', ['id' => 'tree', 'title' => 'Trees and the stacked layout', 'caption' => 'A nested tree model, with <code>inlineOptions: false</code>.'])
        @include('reference._form_example', ['name' => 'tree'])
    </section>

    <section class="ui-section" id="without-relation">
        @include('reference._heading', ['id' => 'without-relation', 'title' => 'Without a relation', 'caption' => '<code>useRelation: false</code> with <code>modelClass</code>, <code>keyFrom</code> and <code>conditions</code>.'])
        @include('reference._form_example', ['name' => 'without-relation'])
    </section>

    <section class="ui-section" id="read-only">
        @include('reference._heading', ['id' => 'read-only', 'title' => 'Read-only', 'caption' => '<code>readOnly</code> shows the record without its buttons.'])
        @include('reference._form_example', ['name' => 'read-only'])
    </section>

    <section class="ui-section" id="preview">
        @include('reference._heading', ['id' => 'preview', 'title' => 'Preview', 'caption' => '<code>disabled</code> shows the record in preview mode.'])
        @include('reference._form_example', ['name' => 'preview'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
