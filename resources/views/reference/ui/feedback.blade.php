@extends('layouts.app')
@section('title', 'Feedback')
@section('content')

@include('reference._header', [
    'intro' => 'Messages and figures for the reader, plus the loading and unsaved states that controls add while a request runs or a form changes.',
])

<section class="ui-section" id="callouts">
    <h2>
        Callout types
    </h2>
    <p class="fg-3">
        <code>Ui::callout()</code> is a fluent element: chain a type, a <code>label</code> and a <code>comment</code>. Each type shows its icon from the icon pack, and <code>tip()</code> is the info type.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.callout-types', 'stacked' => true])
</section>

<section class="ui-section" id="callout-body">
    <h2>
        Callout content
    </h2>
    <p class="fg-3">
        The first argument is the body, as a string, a closure or another element. A closure can return a view or echo its markup.
    </p>
    @include('reference._example', [
        'view' => 'reference.ui.examples.callout-body',
        'stacked' => true,
        'related' => ['reference.ui._callout_checklist' => 'The closure renders'],
    ])
</section>

<section class="ui-section" id="callout-block">
    <h2>
        Callout blocks
    </h2>
    <p class="fg-3">
        <code>slot()</code> captures the markup that follows as the body, up to <code>Ui::end()</code>, which renders the callout. <code>Ui::slot('action')</code> switches the capture to the action area on the right, so a long body and its button stay in plain Blade.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.callout-block', 'stacked' => true])
</section>

<section class="ui-section" id="callout-icons">
    <h2>
        Callout icons and actions
    </h2>
    <p class="fg-3">
        <code>icon()</code> swaps the type's icon for any icon class, or turns it off with <code>false</code>. <code>action()</code> places a button on the right.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.callout-icons', 'stacked' => true])
</section>

<section class="ui-section" id="scoreboard">
    <h2>
        Scoreboard
    </h2>
    <p class="fg-3">
        The scoreboard element lays out key figures in a row that scrolls sideways when it runs out of room. <code>positive</code> and <code>negative</code> add a trend arrow.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.scoreboard', 'stacked' => true])
</section>

<section class="ui-section" id="loader-container">
    <h2>
        Loader container
    </h2>
    <p class="fg-3">
        While a request from inside a <code>loader-container</code> runs, the container dims and ignores clicks. The handler sleeps for a moment so you can see it.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.loader-container', 'stacked' => true])
</section>

<section class="ui-section" id="load-indicator">
    <h2>
        Load indicator
    </h2>
    <p class="fg-3">
        A <code>loading-indicator</code> inside a <code>loading-indicator-container</code> covers it with a spinner, in the default and small sizes or centered.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.load-indicator-markup', 'stacked' => true])
</section>

<section class="ui-section" id="load-indicator-api">
    <h2>
        Load indicator from JavaScript
    </h2>
    <p class="fg-3">
        The <code>LoadIndicator</code> class adds and removes the indicator for you.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.load-indicator-api', 'stacked' => true])
</section>

<section class="ui-section" id="change-monitor">
    <h2>
        Change monitor
    </h2>
    <p class="fg-3">
        <code>data-change-monitor</code> marks the form with <code>oc-data-changed</code> as soon as a field changes, and warns before the page is left. A button with <code>dataChangeMonitorCommit</code> marks it saved.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.change-monitor', 'stacked' => true])
</section>

@endsection
