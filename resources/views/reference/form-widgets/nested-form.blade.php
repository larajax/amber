@extends('layouts.app')
@section('title', 'Nested Form')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>nestedform</code> field renders a form inside a field. On a jsonable attribute it saves the inner fields as one JSON object, and on a singular relation it edits the related record.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="default">
        <h2>
            JSON object
        </h2>
        <p class="fg-3">
            The <code>form</code> option takes the same definition as any form, and the inner fields are named inside the field, such as <code>address[line1]</code>.
        </p>
        @include('reference._form_example', ['name' => 'default'])
    </section>

    <section class="ui-section" id="tabs">
        <h2>
            Tabs
        </h2>
        <p class="fg-3">
            The inner form can have <code>tabs</code> and <code>secondaryTabs</code> of its own.
        </p>
        @include('reference._form_example', ['name' => 'tabs'])
    </section>

    <section class="ui-section" id="no-panel">
        <h2>
            Without the panel
        </h2>
        <p class="fg-3">
            <code>showPanel: false</code> drops the border and background, so the inner fields sit in line with the rest of the form.
        </p>
        @include('reference._form_example', ['name' => 'no-panel'])
    </section>

    <section class="ui-section" id="relation">
        <h2>
            Relation
        </h2>
        <p class="fg-3">
            On a belongsTo relation the inner form edits the related record, which is saved along with the sample. <code>defaultCreate</code> would create and attach a record when the relation is empty.
        </p>
        @include('reference._form_example', ['name' => 'relation'])
    </section>

    <section class="ui-section" id="preview">
        <h2>
            Preview
        </h2>
        <p class="fg-3">
            <code>disabled</code> renders the inner form in preview mode, and the field is left out of the save. This copy reads the address through <code>valueFrom</code>.
        </p>
        @include('reference._form_example', ['name' => 'preview'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
