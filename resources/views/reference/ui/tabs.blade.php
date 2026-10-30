@extends('layouts.app')
@section('title', 'Tabs')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>tab</code> control on plain <code>control-tabs</code> markup, in each style and with its options.',
])

<section class="ui-section" id="tabs-primary">
    @include('reference._heading', ['id' => 'tabs-primary', 'title' => 'Primary tabs', 'caption' => '<code>primary-tabs</code> with <code>data-linkable</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.tabs-primary', 'stacked' => true])
</section>

<section class="ui-section" id="tabs-secondary">
    @include('reference._heading', ['id' => 'tabs-secondary', 'title' => 'Secondary tabs', 'caption' => 'The <code>secondary-tabs</code> style.'])
    @include('reference._example', ['view' => 'reference.ui.examples.tabs-secondary', 'stacked' => true])
</section>

<section class="ui-section" id="tabs-pill">
    @include('reference._heading', ['id' => 'tabs-pill', 'title' => 'Pill tabs', 'caption' => 'The <code>pill-tabs</code> style.'])
    @include('reference._example', ['view' => 'reference.ui.examples.tabs-pill', 'stacked' => true])
</section>

<section class="ui-section" id="tabs-underline">
    @include('reference._heading', ['id' => 'tabs-underline', 'title' => 'Underline tabs', 'caption' => 'The <code>underline-tabs</code> style.'])
    @include('reference._example', ['view' => 'reference.ui.examples.tabs-underline', 'stacked' => true])
</section>

<section class="ui-section" id="tabs-overflow">
    @include('reference._heading', ['id' => 'tabs-overflow', 'title' => 'Overflowing tabs', 'caption' => 'Twelve tabs in a row that scrolls sideways.'])
    @include('reference._example', ['view' => 'reference.ui.examples.tabs-overflow', 'stacked' => true])
</section>

<section class="ui-section" id="tabs-closable">
    @include('reference._heading', ['id' => 'tabs-closable', 'title' => 'Closable tabs', 'caption' => '<code>data-closable</code> and the control\'s <code>addTab()</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.tabs-closable', 'stacked' => true])
</section>

<section class="ui-section" id="tabs-modified">
    @include('reference._heading', ['id' => 'tabs-modified', 'title' => 'Modified tabs', 'caption' => '<code>tab:modified</code> events with <code>data-close-confirmation</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.tabs-modified', 'stacked' => true])
</section>

@endsection
