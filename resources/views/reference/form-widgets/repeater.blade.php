@extends('layouts.app')
@section('title', 'Repeater')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>repeater</code> field repeats a small form as a list of items, saved as JSON or related records.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="default">
        @include('reference._heading', ['id' => 'default', 'title' => 'Items', 'caption' => '<code>form</code>, <code>prompt</code> and <code>titleFrom</code>.'])
        @include('reference._form_example', ['name' => 'default'])
    </section>

    <section class="ui-section" id="limits">
        @include('reference._heading', ['id' => 'limits', 'title' => 'Item limits', 'caption' => '<code>minItems</code> and <code>maxItems</code>.'])
        @include('reference._form_example', ['name' => 'limits'])
    </section>

    <section class="ui-section" id="collapsed">
        @include('reference._heading', ['id' => 'collapsed', 'title' => 'Collapsed items', 'caption' => '<code>itemsExpanded: false</code> shows each item as its title.'])
        @include('reference._form_example', ['name' => 'collapsed'])
    </section>

    <section class="ui-section" id="fixed-order">
        @include('reference._heading', ['id' => 'fixed-order', 'title' => 'Fixed order', 'caption' => '<code>showReorder: false</code> and <code>showDuplicate: false</code>.'])
        @include('reference._form_example', ['name' => 'fixed-order'])
    </section>

    <section class="ui-section" id="groups">
        @include('reference._heading', ['id' => 'groups', 'title' => 'Groups', 'caption' => '<code>groups</code> of item types, with <code>groupKeyFrom</code>.'])
        @include('reference._form_example', ['name' => 'groups'])
    </section>

    <section class="ui-section" id="builder">
        @include('reference._heading', ['id' => 'builder', 'title' => 'Builder', 'caption' => '<code>displayMode: builder</code> with a tabbed group and a nested repeater.'])
        @include('reference._form_example', ['name' => 'builder'])
    </section>

    <section class="ui-section" id="tabs">
        @include('reference._heading', ['id' => 'tabs', 'title' => 'Tabs', 'caption' => "<code>useTabs</code> with each item field's <code>tab</code>."])
        @include('reference._form_example', ['name' => 'tabs'])
    </section>

    <section class="ui-section" id="nested">
        @include('reference._heading', ['id' => 'nested', 'title' => 'Nested repeaters', 'caption' => 'A repeater inside each item.'])
        @include('reference._form_example', ['name' => 'nested'])
    </section>

    <section class="ui-section" id="parent-fields">
        @include('reference._heading', ['id' => 'parent-fields', 'title' => 'Watching the parent form', 'caption' => 'A <code>trigger</code> on the parent form field <code>^step_style</code>.'])
        @include('reference._form_example', ['name' => 'parent-fields'])
    </section>

    <section class="ui-section" id="relation">
        @include('reference._heading', ['id' => 'relation', 'title' => 'Relation', 'caption' => 'Items saved as records of a hasMany relation.'])
        @include('reference._form_example', ['name' => 'relation'])
    </section>

    <section class="ui-section" id="preview">
        @include('reference._heading', ['id' => 'preview', 'title' => 'Preview', 'caption' => '<code>disabled</code> shows the questions through <code>valueFrom</code>.'])
        @include('reference._form_example', ['name' => 'preview'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
