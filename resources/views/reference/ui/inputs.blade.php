@extends('layouts.app')
@section('title', 'Inputs')
@section('content')

@include('reference._header', [
    'intro' => 'Controls that enhance plain inputs: searchable selects, tri-state and range-selected checkboxes, and hotkeys.',
])

<section class="ui-section" id="custom-select">
    @include('reference._heading', ['id' => 'custom-select', 'title' => 'Custom select', 'caption' => '<code>custom-select</code>, <code>select-no-search</code> and option groups.'])
    @include('reference._example', ['view' => 'reference.ui.examples.custom-select', 'stacked' => true])
</section>

<section class="ui-section" id="custom-select-ajax">
    @include('reference._heading', ['id' => 'custom-select-ajax', 'title' => 'Custom select with a handler', 'caption' => '<code>data-handler</code> with <code>data-minimum-input-length</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.custom-select-ajax', 'stacked' => true])
</section>

<section class="ui-section" id="checkbox">
    @include('reference._heading', ['id' => 'checkbox', 'title' => 'Tri-state checkbox', 'caption' => '<code>is-indeterminate</code> starting from <code>data-checked="1"</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.checkbox-tristate', 'stacked' => true])
</section>

<section class="ui-section" id="checkbox-range">
    @include('reference._heading', ['id' => 'checkbox-range', 'title' => 'Checkbox range selection', 'caption' => '<kbd>Shift</kbd> and click through <code>jax.checkboxRangeRegisterClick()</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.checkbox-range', 'stacked' => true])
</section>

<section class="ui-section" id="input-hotkey">
    @include('reference._heading', ['id' => 'input-hotkey', 'title' => 'Input hotkey', 'caption' => '<code>data-hotkey</code> on a handler button and a link.'])
    @include('reference._example', ['view' => 'reference.ui.examples.input-hotkey'])
</section>

@endsection
