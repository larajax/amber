@extends('layouts.app')
@section('title', 'Tabs')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>tab</code> control works on any <code>control-tabs</code> markup: a <code>nav-tabs</code> list and a <code>tab-content</code> with one pane per tab, matched by their order. Forms use the same control for their tabs.',
])

<section class="ui-section" id="tabs-primary">
    <h2>
        Primary tabs
    </h2>
    <p class="fg-3">
        <code>primary-tabs</code> is the main style. <code>data-linkable</code> keeps the open tab in the address, so a reload or a shared link opens it again.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.tabs-primary', 'stacked' => true])
</section>

<section class="ui-section" id="tabs-secondary">
    <h2>
        Secondary tabs
    </h2>
    <p class="fg-3">
        The <code>secondary-tabs</code> class gives a lighter style.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.tabs-secondary', 'stacked' => true])
</section>

<section class="ui-section" id="tabs-pill">
    <h2>
        Pill tabs
    </h2>
    <p class="fg-3">
        <code>pill-tabs</code> joins the tabs into a segmented control.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.tabs-pill', 'stacked' => true])
</section>

<section class="ui-section" id="tabs-underline">
    <h2>
        Underline tabs
    </h2>
    <p class="fg-3">
        <code>underline-tabs</code> marks the open tab with a line under its title.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.tabs-underline', 'stacked' => true])
</section>

<section class="ui-section" id="tabs-overflow">
    <h2>
        Overflowing tabs
    </h2>
    <p class="fg-3">
        Tabs that do not fit scroll sideways, by dragging or with the mouse wheel, and a small arrow at each edge shows there are more.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.tabs-overflow', 'stacked' => true])
</section>

<section class="ui-section" id="tabs-closable">
    <h2>
        Closable tabs
    </h2>
    <p class="fg-3">
        <code>data-closable</code> adds a close button to each tab, and the control's <code>addTab()</code> method opens new ones.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.tabs-closable', 'stacked' => true])
</section>

<section class="ui-section" id="tabs-modified">
    <h2>
        Modified tabs
    </h2>
    <p class="fg-3">
        A <code>tab:modified</code> event from inside a pane swaps its close button for a dot, and <code>data-close-confirmation</code> asks before such a tab closes. <code>tab:unmodified</code> clears the mark.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.tabs-modified', 'stacked' => true])
</section>

@endsection
