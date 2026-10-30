@extends('layouts.app')
@section('title', 'Tag List')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>taglist</code> field picks several values as tags, saved as an array, a string or a relation.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="array">
        @include('reference._heading', ['id' => 'array', 'title' => 'Array mode', 'caption' => '<code>mode: array</code> with <code>customTags</code> and <code>placeholder</code>.'])
        @include('reference._form_example', ['name' => 'array'])
    </section>

    <section class="ui-section" id="string">
        @include('reference._heading', ['id' => 'string', 'title' => 'String mode', 'caption' => '<code>mode: string</code> with <code>separator: space</code>.'])
        @include('reference._form_example', ['name' => 'string'])
    </section>

    <section class="ui-section" id="options">
        @include('reference._heading', ['id' => 'options', 'title' => 'Fixed options', 'caption' => '<code>options</code> with <code>maxItems</code>.'])
        @include('reference._form_example', ['name' => 'options'])
    </section>

    <section class="ui-section" id="use-key">
        @include('reference._heading', ['id' => 'use-key', 'title' => 'Saving labels', 'caption' => '<code>useKey: false</code> saves the option labels.'])
        @include('reference._form_example', ['name' => 'use-key'])
    </section>

    <section class="ui-section" id="relation">
        @include('reference._heading', ['id' => 'relation', 'title' => 'Relation mode', 'caption' => 'A belongsToMany relation with <code>nameFrom</code>.'])
        @include('reference._form_example', ['name' => 'relation'])
    </section>

    <section class="ui-section" id="relation-custom">
        @include('reference._heading', ['id' => 'relation-custom', 'title' => 'Creating related records', 'caption' => '<code>customTags</code> in relation mode.'])
        @include('reference._form_example', ['name' => 'relation-custom'])
    </section>

    <section class="ui-section" id="read-only">
        @include('reference._heading', ['id' => 'read-only', 'title' => 'Read-only', 'caption' => '<code>readOnly</code> lists the tags without an input.'])
        @include('reference._form_example', ['name' => 'read-only'])
    </section>

    <section class="ui-section" id="preview">
        @include('reference._heading', ['id' => 'preview', 'title' => 'Preview', 'caption' => '<code>disabled</code> lists the picked labels.'])
        @include('reference._form_example', ['name' => 'preview'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
