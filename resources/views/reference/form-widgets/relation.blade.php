@extends('layouts.app')
@section('title', 'Relation')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>relation</code> field picks related records from a dropdown or a checkbox list.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="dropdown">
        @include('reference._heading', ['id' => 'dropdown', 'title' => 'Dropdown', 'caption' => 'A belongsTo relation with <code>nameFrom</code>, <code>emptyOption</code> and <code>defaultSort</code>.'])
        @include('reference._form_example', ['name' => 'dropdown'])
    </section>

    <section class="ui-section" id="checkbox-list">
        @include('reference._heading', ['id' => 'checkbox-list', 'title' => 'Checkbox list', 'caption' => 'A belongsToMany relation with <code>scope</code> and <code>emptyOption</code>.'])
        @include('reference._form_example', ['name' => 'checkbox-list'])
    </section>

    <section class="ui-section" id="options-query">
        @include('reference._heading', ['id' => 'options-query', 'title' => 'Shaping the options', 'caption' => '<code>select</code>, <code>conditions</code> and <code>excludeFrom</code>.'])
        @include('reference._form_example', ['name' => 'options-query'])
    </section>

    <section class="ui-section" id="tree">
        @include('reference._heading', ['id' => 'tree', 'title' => 'Tree dropdown', 'caption' => 'A belongsTo relation to a nested tree model.'])
        @include('reference._form_example', ['name' => 'tree'])
    </section>

    <section class="ui-section" id="tree-checkbox-list">
        @include('reference._heading', ['id' => 'tree-checkbox-list', 'title' => 'Tree checkbox list', 'caption' => 'A belongsToMany relation to a nested tree model.'])
        @include('reference._form_example', ['name' => 'tree-checkbox-list'])
    </section>

    <section class="ui-section" id="quick-create">
        @include('reference._heading', ['id' => 'quick-create', 'title' => 'Quick create', 'caption' => '<code>quickCreate</code> with a popup form in the <code>quickcreate</code> context.'])
        @include('reference._form_example', ['name' => 'quick-create'])
        <div class="ui-example">
            @include('reference._source', [
                'path' => 'resources/ui/reference/form-widgets/relation/quick-create-form.yaml',
                'caption' => 'The popup form',
            ])
        </div>
    </section>

    <section class="ui-section" id="read-only">
        @include('reference._heading', ['id' => 'read-only', 'title' => 'Read-only', 'caption' => '<code>readOnly</code> shows the picked option as text.'])
        @include('reference._form_example', ['name' => 'read-only'])
    </section>

    <section class="ui-section" id="disabled">
        @include('reference._heading', ['id' => 'disabled', 'title' => 'Disabled', 'caption' => '<code>disabled</code> locks the options.'])
        @include('reference._form_example', ['name' => 'disabled'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
