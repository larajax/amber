@extends('layouts.app')
@section('title', 'Controls')
@section('content')

<h1 class="mb-3">
    Controls
</h1>
<p class="fg-3 mb-5">
    The widgets are built on controls you can also use directly. Each one starts from data attributes in plain markup, with no PHP needed unless it calls a handler.
</p>

@include('ui_elements._nav')

<section class="ui-section" id="popover">
    <h2>
        Popover
    </h2>
    <p class="fg-3">
        <code>data-control="popover"</code> opens a panel next to the element. <code>data-content-from</code> copies the content from a template, <code>data-placement</code> picks the side, and <code>data-modal</code> dims the page behind it.
    </p>
    @include('ui_elements._example', ['name' => 'popover'])
</section>

<section class="ui-section" id="input-preset">
    <h2>
        Input preset
    </h2>
    <p class="fg-3">
        <code>data-input-preset</code> fills a field from another as you type, converted by <code>data-input-preset-type</code>. Editing the destination by hand stops the copying.
    </p>
    @include('ui_elements._example', ['name' => 'input-preset', 'stacked' => true])
</section>

<section class="ui-section" id="input-trigger">
    <h2>
        Input trigger
    </h2>
    <p class="fg-3">
        <code>data-trigger</code> shows, hides, enables or disables an element from another field's state: <code>checked</code>, <code>unchecked</code> or <code>value[...]</code>.
    </p>
    @include('ui_elements._example', ['name' => 'input-trigger', 'stacked' => true])
</section>

<section class="ui-section" id="input-hotkey">
    <h2>
        Input hotkey
    </h2>
    <p class="fg-3">
        <code>data-hotkey</code> clicks any visible element from the keyboard, whether it runs a handler or follows a link.
    </p>
    @include('ui_elements._example', ['name' => 'input-hotkey'])
</section>

<section class="ui-section" id="custom-select">
    <h2>
        Custom select
    </h2>
    <p class="fg-3">
        A <code>select</code> with the <code>custom-select</code> class gets a searchable dropdown. <code>select-no-search</code> hides the search box, and option groups carry over.
    </p>
    @include('ui_elements._example', ['name' => 'custom-select', 'stacked' => true])
</section>

<section class="ui-section" id="custom-select-ajax">
    <h2>
        Custom select with a handler
    </h2>
    <p class="fg-3">
        <code>data-handler</code> loads the options from the server as you search. The handler returns a <code>results</code> array of <code>id</code> and <code>text</code> pairs.
    </p>
    @include('ui_elements._example', ['name' => 'custom-select-ajax', 'stacked' => true])
</section>

<section class="ui-section" id="checkbox">
    <h2>
        Tri-state checkbox
    </h2>
    <p class="fg-3">
        A <code>form-field</code> with the <code>is-indeterminate</code> class cycles its checkbox through unchecked, mixed and checked. <code>data-checked</code> sets the starting state.
    </p>
    @include('ui_elements._example', ['name' => 'checkbox-tristate', 'stacked' => true])
</section>

<section class="ui-section" id="checkbox-range">
    <h2>
        Checkbox range selection
    </h2>
    <p class="fg-3">
        Hold <kbd>Shift</kbd> and click a second checkbox to set every checkbox between the two. The list widget uses the same helper for its row checkboxes, alongside its select all checkbox.
    </p>
    @include('ui_elements._example', ['name' => 'checkbox-range', 'stacked' => true])
</section>

<section class="ui-section" id="rowlink">
    <h2>
        Row link
    </h2>
    <p class="fg-3">
        <code>data-control="rowlink"</code> makes each table row act as the first link in it, which can open a page, a popup or a request. Cells with the <code>nolink</code> class are left alone.
    </p>
    @include('ui_elements._example', ['name' => 'rowlink', 'stacked' => true])
</section>

@endsection
