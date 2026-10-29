@extends('layouts.app')
@section('title', 'Feedback and Layout')
@section('content')

<h1 class="mb-3">
    Feedback and Layout
</h1>
<p class="fg-3 mb-5">
    Messages, search, figures and tabs, plus the loading and unsaved states that controls add while a request runs or a form changes.
</p>

@include('ui_elements._nav')

<section class="ui-section" id="callouts">
    <h2>
        Callout types
    </h2>
    <p class="fg-3">
        <code>Ui::callout()</code> is a fluent element: chain a type, a <code>label</code> and a <code>comment</code>. Each type shows its icon from the icon pack.
    </p>
    @include('ui_elements._example', ['name' => 'callout-types', 'stacked' => true])
</section>

<section class="ui-section" id="callout-body">
    <h2>
        Callout content
    </h2>
    <p class="fg-3">
        The first argument is the body, as a string, a closure or another element. A closure can return a view.
    </p>
    @include('ui_elements._example', [
        'name' => 'callout-body',
        'stacked' => true,
        'related' => ['ui_elements._callout_checklist' => 'The closure renders'],
    ])
</section>

<section class="ui-section" id="callout-icons">
    <h2>
        Callout icons and actions
    </h2>
    <p class="fg-3">
        <code>icon()</code> swaps the type's icon for any icon class, or turns it off with <code>false</code>. <code>action()</code> places a button on the right.
    </p>
    @include('ui_elements._example', ['name' => 'callout-icons', 'stacked' => true])
</section>

<section class="ui-section" id="search-input">
    <h2>
        Search input
    </h2>
    <p class="fg-3">
        <code>Ui::searchInput()</code> renders a search field with a clear button.
    </p>
    @include('ui_elements._example', ['name' => 'search-input', 'stacked' => true])
</section>

<section class="ui-section" id="search-input-ajax">
    <h2>
        Search input with a handler
    </h2>
    <p class="fg-3">
        With a <code>handler</code>, the input sends a request half a second after typing stops. Here the handler updates the results below.
    </p>
    @include('ui_elements._example', [
        'name' => 'search-input-ajax',
        'stacked' => true,
        'related' => ['ui_elements._city_results' => 'The handler renders'],
    ])
</section>

<section class="ui-section" id="scoreboard">
    <h2>
        Scoreboard
    </h2>
    <p class="fg-3">
        The scoreboard element lays out key figures in a row that scrolls sideways when it runs out of room. <code>positive</code> and <code>negative</code> add a trend arrow.
    </p>
    @include('ui_elements._example', ['name' => 'scoreboard', 'stacked' => true])
</section>

<section class="ui-section" id="tabs-primary">
    <h2>
        Primary tabs
    </h2>
    <p class="fg-3">
        The tab control works on any <code>control-tabs</code> markup. <code>data-linkable</code> keeps the open tab in the address.
    </p>
    @include('ui_elements._example', ['name' => 'tabs-primary', 'stacked' => true])
</section>

<section class="ui-section" id="tabs-secondary">
    <h2>
        Secondary tabs
    </h2>
    <p class="fg-3">
        The <code>secondary-tabs</code> class gives a lighter style.
    </p>
    @include('ui_elements._example', ['name' => 'tabs-secondary', 'stacked' => true])
</section>

<section class="ui-section" id="tabs-closable">
    <h2>
        Closable tabs
    </h2>
    <p class="fg-3">
        <code>data-closable</code> adds a close button to each tab, and the control's <code>addTab()</code> method opens new ones.
    </p>
    @include('ui_elements._example', ['name' => 'tabs-closable', 'stacked' => true])
</section>

<section class="ui-section" id="loader-container">
    <h2>
        Loader container
    </h2>
    <p class="fg-3">
        While a request from inside a <code>loader-container</code> runs, the container dims and ignores clicks. The handler sleeps for a moment so you can see it.
    </p>
    @include('ui_elements._example', ['name' => 'loader-container', 'stacked' => true])
</section>

<section class="ui-section" id="load-indicator">
    <h2>
        Load indicator
    </h2>
    <p class="fg-3">
        A <code>loading-indicator</code> inside a <code>loading-indicator-container</code> covers it with a spinner, in the default and small sizes or centered.
    </p>
    @include('ui_elements._example', ['name' => 'load-indicator-markup', 'stacked' => true])
</section>

<section class="ui-section" id="load-indicator-api">
    <h2>
        Load indicator from JavaScript
    </h2>
    <p class="fg-3">
        The <code>LoadIndicator</code> class adds and removes the indicator for you.
    </p>
    @include('ui_elements._example', ['name' => 'load-indicator-api', 'stacked' => true])
</section>

<section class="ui-section" id="change-monitor">
    <h2>
        Change monitor
    </h2>
    <p class="fg-3">
        <code>data-change-monitor</code> marks the form with <code>oc-data-changed</code> as soon as a field changes, and warns before the page is left. A button with <code>dataChangeMonitorCommit</code> marks it saved.
    </p>
    @include('ui_elements._example', ['name' => 'change-monitor', 'stacked' => true])
</section>

@endsection
