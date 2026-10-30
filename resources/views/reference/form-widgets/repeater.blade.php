@extends('layouts.app')
@section('title', 'Repeater')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>repeater</code> field repeats a small form as a list of items. On a jsonable attribute it saves the items as a JSON array, and on a hasMany relation each item is a related record.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="default">
        <h2>
            Items
        </h2>
        <p class="fg-3">
            <code>form</code> defines the fields of each item, <code>prompt</code> labels the add button, and <code>titleFrom</code> names a field whose value titles the item. Drag the handle to sort the items, check several to drag them together, and use the item menu to duplicate, move or remove one.
        </p>
        @include('reference._form_example', ['name' => 'default'])
    </section>

    <section class="ui-section" id="limits">
        <h2>
            Item limits
        </h2>
        <p class="fg-3">
            <code>minItems</code> starts the field with that many empty items and stops removal below it. <code>maxItems</code> hides the add button once the field is full.
        </p>
        @include('reference._form_example', ['name' => 'limits'])
    </section>

    <section class="ui-section" id="collapsed">
        <h2>
            Collapsed items
        </h2>
        <p class="fg-3">
            <code>itemsExpanded: false</code> shows each item as its title, and opening one closes the others.
        </p>
        @include('reference._form_example', ['name' => 'collapsed'])
    </section>

    <section class="ui-section" id="fixed-order">
        <h2>
            Fixed order
        </h2>
        <p class="fg-3">
            <code>showReorder: false</code> removes the drag handle and the move options, and <code>showDuplicate: false</code> removes Duplicate from the item menu.
        </p>
        @include('reference._form_example', ['name' => 'fixed-order'])
    </section>

    <section class="ui-section" id="groups">
        <h2>
            Groups
        </h2>
        <p class="fg-3">
            <code>groups</code> replaces <code>form</code> with several item types, each with a name, description, icon and fields, and the add button opens a menu of them. Each saved item records its group under <code>groupKeyFrom</code>, which defaults to <code>_group</code>.
        </p>
        @include('reference._form_example', ['name' => 'groups'])
    </section>

    <section class="ui-section" id="builder">
        <h2>
            Builder
        </h2>
        <p class="fg-3">
            <code>displayMode: builder</code> lists the items in a sidebar and edits one at a time. A group can set <code>useTabs</code> for its own fields, and a group field can be another repeater.
        </p>
        @include('reference._form_example', ['name' => 'builder'])
    </section>

    <section class="ui-section" id="tabs">
        <h2>
            Tabs
        </h2>
        <p class="fg-3">
            <code>useTabs</code> moves the item fields into tabs, named by each field's <code>tab</code>.
        </p>
        @include('reference._form_example', ['name' => 'tabs'])
    </section>

    <section class="ui-section" id="nested">
        <h2>
            Nested repeaters
        </h2>
        <p class="fg-3">
            An item field can be a repeater, saved as an array inside each item.
        </p>
        @include('reference._form_example', ['name' => 'nested'])
    </section>

    <section class="ui-section" id="parent-fields">
        <h2>
            Watching the parent form
        </h2>
        <p class="fg-3">
            Item fields are named inside the repeater, so a trigger reaches a field of the parent form with a leading <code>^</code>. Pick Detailed to show the details of every step.
        </p>
        @include('reference._form_example', ['name' => 'parent-fields'])
    </section>

    <section class="ui-section" id="relation">
        <h2>
            Relation
        </h2>
        <p class="fg-3">
            On a hasMany relation, adding an item creates a related record held against the session key, and Save commits the items with their order.
        </p>
        @include('reference._form_example', ['name' => 'relation'])
    </section>

    <section class="ui-section" id="preview">
        <h2>
            Preview
        </h2>
        <p class="fg-3">
            <code>disabled</code> renders the items in preview mode with expand and collapse only, and the field is left out of the save. This copy reads the questions through <code>valueFrom</code>.
        </p>
        @include('reference._form_example', ['name' => 'preview'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
