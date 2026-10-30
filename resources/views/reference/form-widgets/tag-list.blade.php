@extends('layouts.app')
@section('title', 'Tag List')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>taglist</code> field picks several values as tags. Its <code>mode</code> follows the attribute unless set: a jsonable attribute stores an array, a relation stores related records, and anything else stores a string.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="array">
        <h2>
            Array mode
        </h2>
        <p class="fg-3">
            <code>customTags</code> lets the user type new tags, entered with Enter or a comma, and <code>placeholder</code> fills the empty field.
        </p>
        @include('reference._form_example', ['name' => 'array'])
    </section>

    <section class="ui-section" id="string">
        <h2>
            String mode
        </h2>
        <p class="fg-3">
            String mode joins the tags into one value with <code>separator</code>, either <code>comma</code> or <code>space</code>, which also ends a typed tag.
        </p>
        @include('reference._form_example', ['name' => 'string'])
    </section>

    <section class="ui-section" id="options">
        <h2>
            Fixed options
        </h2>
        <p class="fg-3">
            <code>options</code> limits the tags to a list, saved by their keys, and <code>maxItems</code> caps how many can be picked.
        </p>
        @include('reference._form_example', ['name' => 'options'])
    </section>

    <section class="ui-section" id="use-key">
        <h2>
            Saving labels
        </h2>
        <p class="fg-3">
            <code>useKey: false</code> saves the option labels instead of their keys. Custom tags always save the text that was typed.
        </p>
        @include('reference._form_example', ['name' => 'use-key'])
    </section>

    <section class="ui-section" id="relation">
        <h2>
            Relation mode
        </h2>
        <p class="fg-3">
            On a belongsToMany relation the options are the related records, named by <code>nameFrom</code>, and saving syncs the relation.
        </p>
        @include('reference._form_example', ['name' => 'relation'])
    </section>

    <section class="ui-section" id="relation-custom">
        <h2>
            Creating related records
        </h2>
        <p class="fg-3">
            With <code>customTags</code> in relation mode, a typed tag that matches no record creates one, setting its <code>nameFrom</code> column.
        </p>
        @include('reference._form_example', ['name' => 'relation-custom'])
    </section>

    <section class="ui-section" id="read-only">
        <h2>
            Read-only
        </h2>
        <p class="fg-3">
            <code>readOnly</code> lists the tags without an input and keeps them in hidden inputs. This copy reads the tags through <code>valueFrom</code>, and its underscore name keeps it out of the save.
        </p>
        @include('reference._form_example', ['name' => 'read-only'])
    </section>

    <section class="ui-section" id="preview">
        <h2>
            Preview
        </h2>
        <p class="fg-3">
            <code>disabled</code> lists the picked labels in preview mode, and the field is left out of the save.
        </p>
        @include('reference._form_example', ['name' => 'preview'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
