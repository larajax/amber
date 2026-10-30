@extends('layouts.app')
@section('title', 'Feedback')
@section('content')

@include('reference._header', [
    'intro' => 'Callouts, scoreboards, and the loading and unsaved states that controls add.',
])

<section class="ui-section" id="callouts">
    @include('reference._heading', ['id' => 'callouts', 'title' => 'Callout types', 'caption' => 'Info, <code>success</code>, <code>warning</code> and <code>danger</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.callout-types', 'stacked' => true])
</section>

<section class="ui-section" id="callout-body">
    @include('reference._heading', ['id' => 'callout-body', 'title' => 'Callout body', 'caption' => 'A body between <code>slot()</code> and <code>Ui::end()</code>.'])
    @include('reference._example', [
        'view' => 'reference.ui.examples.callout-body',
        'stacked' => true,
        'related' => ['reference.ui._callout_checklist' => 'The included view'],
    ])
</section>

<section class="ui-section" id="callout-action">
    @include('reference._heading', ['id' => 'callout-action', 'title' => 'Callout actions', 'caption' => '<code>Ui::slot(\'action\')</code> after a body, and <code>slot(\'action\')</code> alone.'])
    @include('reference._example', ['view' => 'reference.ui.examples.callout-action', 'stacked' => true])
</section>

<section class="ui-section" id="callout-icons">
    @include('reference._heading', ['id' => 'callout-icons', 'title' => 'Callout icons', 'caption' => '<code>icon</code> with an icon class, and <code>icon: false</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.callout-icons', 'stacked' => true])
</section>

<section class="ui-section" id="scoreboard">
    @include('reference._heading', ['id' => 'scoreboard', 'title' => 'Scoreboard', 'caption' => 'Four figures, with <code>positive</code> and <code>negative</code> trends.'])
    @include('reference._example', ['view' => 'reference.ui.examples.scoreboard', 'stacked' => true])
</section>

<section class="ui-section" id="loader-container">
    @include('reference._heading', ['id' => 'loader-container', 'title' => 'Loader container', 'caption' => '<code>data-control="loader-container"</code> around a slow request.'])
    @include('reference._example', ['view' => 'reference.ui.examples.loader-container', 'stacked' => true])
</section>

<section class="ui-section" id="load-indicator">
    @include('reference._heading', ['id' => 'load-indicator', 'title' => 'Load indicator', 'caption' => 'The default, <code>size-small</code> and <code>indicator-center</code> indicators.'])
    @include('reference._example', ['view' => 'reference.ui.examples.load-indicator-markup', 'stacked' => true])
</section>

<section class="ui-section" id="load-indicator-api">
    @include('reference._heading', ['id' => 'load-indicator-api', 'title' => 'Load indicator from JavaScript', 'caption' => '<code>LoadIndicator.show()</code> and <code>LoadIndicator.hide()</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.load-indicator-api', 'stacked' => true])
</section>

<section class="ui-section" id="change-monitor">
    @include('reference._heading', ['id' => 'change-monitor', 'title' => 'Change monitor', 'caption' => '<code>data-change-monitor</code> with a <code>dataChangeMonitorCommit</code> save button.'])
    @include('reference._example', ['view' => 'reference.ui.examples.change-monitor', 'stacked' => true])
</section>

@endsection
