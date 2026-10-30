@extends('layouts.app')
@section('title', 'Inputs')
@section('content')

@include('reference._header', [
    'intro' => 'Controls that enhance plain inputs from markup: searchable selects, tri-state and range-selected checkboxes, and keyboard shortcuts. Form fields use the same controls.',
])

<section class="ui-section" id="custom-select">
    <h2>
        Custom select
    </h2>
    <p class="fg-3">
        A <code>select</code> with the <code>custom-select</code> class gets a searchable dropdown. <code>select-no-search</code> hides the search box, and option groups carry over.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.custom-select', 'stacked' => true])
</section>

<section class="ui-section" id="custom-select-ajax">
    <h2>
        Custom select with a handler
    </h2>
    <p class="fg-3">
        <code>data-handler</code> loads the options from the server as you search, once <code>data-minimum-input-length</code> characters are typed. The handler returns a <code>results</code> array of <code>id</code> and <code>text</code> pairs.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.custom-select-ajax', 'stacked' => true])
</section>

<section class="ui-section" id="checkbox">
    <h2>
        Tri-state checkbox
    </h2>
    <p class="fg-3">
        A <code>form-field</code> with the <code>is-indeterminate</code> class cycles its checkbox through unchecked, mixed and checked. <code>data-checked</code> sets the starting state: <code>0</code>, <code>1</code> or <code>2</code>.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.checkbox-tristate', 'stacked' => true])
</section>

<section class="ui-section" id="checkbox-range">
    <h2>
        Checkbox range selection
    </h2>
    <p class="fg-3">
        Hold <kbd>Shift</kbd> and click a second checkbox to set every checkbox between the two. The list widget uses the same helper for its row checkboxes.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.checkbox-range', 'stacked' => true])
</section>

<section class="ui-section" id="input-hotkey">
    <h2>
        Input hotkey
    </h2>
    <p class="fg-3">
        <code>data-hotkey</code> clicks any visible element from the keyboard, whether it runs a handler or follows a link. Separate several shortcuts with commas.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.input-hotkey'])
</section>

@endsection
