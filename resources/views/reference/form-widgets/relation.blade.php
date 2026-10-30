@extends('layouts.app')
@section('title', 'Relation')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>relation</code> field picks records through a model relation. A belongsTo or hasOne relation renders as a dropdown, and a belongsToMany or hasMany relation as a checkbox list, with the options read from the related model.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="dropdown">
        <h2>
            Dropdown
        </h2>
        <p class="fg-3">
            <code>nameFrom</code> names each option from a column of the related model, <code>emptyOption</code> adds a first option that saves no record, and <code>defaultSort</code> orders the options.
        </p>
        @include('reference._form_example', ['name' => 'dropdown'])
    </section>

    <section class="ui-section" id="checkbox-list">
        <h2>
            Checkbox list
        </h2>
        <p class="fg-3">
            A belongsToMany relation saves every checked record. <code>scope</code> names a model scope that narrows the options, and on a checkbox list <code>emptyOption</code> is the message shown when there are none.
        </p>
        @include('reference._form_example', ['name' => 'checkbox-list'])
    </section>

    <section class="ui-section" id="options-query">
        <h2>
            Shaping the options
        </h2>
        <p class="fg-3">
            <code>select</code> builds each label in SQL, <code>conditions</code> filters with a raw where clause, and <code>excludeFrom</code> leaves out the keys held in another attribute, here the saved owner.
        </p>
        @include('reference._form_example', ['name' => 'options-query'])
    </section>

    <section class="ui-section" id="tree">
        <h2>
            Tree dropdown
        </h2>
        <p class="fg-3">
            When the related model is a nested tree, the options follow the tree and each child is indented under its parent.
        </p>
        @include('reference._form_example', ['name' => 'tree'])
    </section>

    <section class="ui-section" id="tree-checkbox-list">
        <h2>
            Tree checkbox list
        </h2>
        <p class="fg-3">
            A belongsToMany relation to a tree nests the checkboxes the same way.
        </p>
        @include('reference._form_example', ['name' => 'tree-checkbox-list'])
    </section>

    <section class="ui-section" id="quick-create">
        <h2>
            Quick create
        </h2>
        <p class="fg-3">
            <code>quickCreate</code> adds a Create New option to the dropdown, which opens the related model's form in a popup and selects the record it creates. The popup form renders in the <code>quickcreate</code> context, so the members field below stays out of it.
        </p>
        @include('reference._form_example', ['name' => 'quick-create'])
        <div class="ui-example">
            @include('reference._source', [
                'path' => 'resources/ui/reference/form-widgets/relation/quick-create-form.yaml',
                'caption' => 'The popup form',
            ])
        </div>
    </section>

    <section class="ui-section" id="read-only">
        <h2>
            Read-only
        </h2>
        <p class="fg-3">
            <code>readOnly</code> shows the picked option as text and keeps its key in a hidden input. This copy reads the owner through <code>valueFrom</code>, and its underscore name keeps it out of the save.
        </p>
        @include('reference._form_example', ['name' => 'read-only'])
    </section>

    <section class="ui-section" id="disabled">
        <h2>
            Disabled
        </h2>
        <p class="fg-3">
            <code>disabled</code> locks the options, and the field is left out of the save.
        </p>
        @include('reference._form_example', ['name' => 'disabled'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
