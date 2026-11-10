@extends('layouts.app')
@section('title', 'Charts')
@section('content')

@include('reference._header', [
    'intro' => 'Pie, bar and line charts drawn with Chart.js, which loads the first time a chart shows.',
])

<section class="ui-section" id="chart-pie">
    @include('reference._heading', ['id' => 'chart-pie', 'title' => 'Pie charts', 'caption' => '<code>Ui::pieChart()</code> with <code>centerText</code>, and <code>centered</code> with <code>wrapLegend</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.chart-pie', 'stacked' => true])
</section>

<section class="ui-section" id="chart-values">
    @include('reference._heading', ['id' => 'chart-values', 'title' => 'Values', 'caption' => 'The three forms a value takes, as in an options list.'])
    @include('reference._example', ['view' => 'reference.ui.examples.chart-values', 'stacked' => true])
</section>

<section class="ui-section" id="chart-bar">
    @include('reference._heading', ['id' => 'chart-bar', 'title' => 'Bar charts', 'caption' => '<code>Ui::barChart()</code> across the page with <code>fullWidth</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.chart-bar', 'stacked' => true])
</section>

<section class="ui-section" id="chart-line">
    @include('reference._heading', ['id' => 'chart-line', 'title' => 'Line charts', 'caption' => '<code>Ui::lineChart()</code> with values keyed by date, and <code>timeMode</code> shading the weekends.'])
    @include('reference._example', ['view' => 'reference.ui.examples.chart-line', 'stacked' => true])
</section>

<section class="ui-section" id="chart-zoom">
    @include('reference._heading', ['id' => 'chart-zoom', 'title' => 'Zooming', 'caption' => '<code>zoomable</code> with a <code>resetZoomLink</code>, from <code>[date, value]</code> points.'])
    @include('reference._example', ['view' => 'reference.ui.examples.chart-zoom', 'stacked' => true])
</section>

<section class="ui-section" id="chart-config">
    @include('reference._heading', ['id' => 'chart-config', 'title' => 'Chart.js options', 'caption' => '<code>config</code> merges Chart.js options over the chart\'s own.'])
    @include('reference._example', ['view' => 'reference.ui.examples.chart-config', 'stacked' => true])
</section>

<section class="ui-section" id="chart-events">
    @include('reference._heading', ['id' => 'chart-events', 'title' => 'Events', 'caption' => '<code>chart-bar:ready</code> hands over the Chart.js instance.'])
    @include('reference._example', ['view' => 'reference.ui.examples.chart-events', 'stacked' => true])
</section>

<section class="ui-section" id="chart-markup">
    @include('reference._heading', ['id' => 'chart-markup', 'title' => 'Markup', 'caption' => 'A line chart written by hand, with Flot style <code>data-chart-options</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.chart-markup', 'stacked' => true])
</section>

@endsection
