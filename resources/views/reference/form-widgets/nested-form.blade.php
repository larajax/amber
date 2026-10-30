@extends('layouts.app')
@section('title', 'Nested Form')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>nestedform</code> field renders a form inside a field, saved as JSON or a related record.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="default">
        @include('reference._heading', ['id' => 'default', 'title' => 'JSON object', 'caption' => 'Inner fields saved as one JSON object, with the <code>form</code> option.'])
        @include('reference._form_example', ['name' => 'default'])
    </section>

    <section class="ui-section" id="tabs">
        @include('reference._heading', ['id' => 'tabs', 'title' => 'Tabs', 'caption' => 'An inner form with <code>tabs</code> of its own.'])
        @include('reference._form_example', ['name' => 'tabs'])
    </section>

    <section class="ui-section" id="no-panel">
        @include('reference._heading', ['id' => 'no-panel', 'title' => 'Without the panel', 'caption' => '<code>showPanel: false</code> drops the border and background.'])
        @include('reference._form_example', ['name' => 'no-panel'])
    </section>

    <section class="ui-section" id="relation">
        @include('reference._heading', ['id' => 'relation', 'title' => 'Relation', 'caption' => 'Inner fields editing the record of a belongsTo relation.'])
        @include('reference._form_example', ['name' => 'relation'])
    </section>

    <section class="ui-section" id="preview">
        @include('reference._heading', ['id' => 'preview', 'title' => 'Preview', 'caption' => '<code>disabled</code> shows the address through <code>valueFrom</code>.'])
        @include('reference._form_example', ['name' => 'preview'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
