@extends('layouts.app')
@section('title', 'Scoreboards')
@section('content')

@include('reference._header', [
    'intro' => 'A row of key figures from <code>Ui::scoreboard()</code>, with trends, sparklines, markers, goal meters, links, thumbnails and charts.',
])

<section class="ui-section" id="scoreboard-figures">
    @include('reference._heading', ['id' => 'scoreboard-figures', 'title' => 'Figures', 'caption' => 'A <code>label</code>, <code>value</code> and <code>comment</code>, a <code>unit</code>, <code>success</code> and <code>danger</code>, and a <code>null</code> value.'])
    @include('reference._example', ['view' => 'reference.ui.examples.scoreboard-figures', 'stacked' => true])
</section>

<section class="ui-section" id="scoreboard-trends">
    @include('reference._heading', ['id' => 'scoreboard-trends', 'title' => 'Trends', 'caption' => 'A <code>change</code> or a <code>compare</code>, <code>inverse</code> for lower is better, and a <code>sparkline</code> of recent values.'])
    @include('reference._example', ['view' => 'reference.ui.examples.scoreboard-trends', 'stacked' => true])
</section>

<section class="ui-section" id="scoreboard-markers">
    @include('reference._heading', ['id' => 'scoreboard-markers', 'title' => 'Markers', 'caption' => 'An <code>icon</code> tile, tinted with a <code>color</code>, a <code>color</code> swatch and an <code>image</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.scoreboard-markers', 'stacked' => true])
</section>

<section class="ui-section" id="scoreboard-meters">
    @include('reference._heading', ['id' => 'scoreboard-meters', 'title' => 'Goal meters', 'caption' => 'A <code>meter</code> percentage, and <code>inverse</code> for a rate where higher is worse.'])
    @include('reference._example', ['view' => 'reference.ui.examples.scoreboard-meters', 'stacked' => true])
</section>

<section class="ui-section" id="scoreboard-links">
    @include('reference._heading', ['id' => 'scoreboard-links', 'title' => 'Links and help', 'caption' => 'An <code>href</code> makes the item a link, and a <code>tooltip</code> explains the figure.'])
    @include('reference._example', ['view' => 'reference.ui.examples.scoreboard-links', 'stacked' => true])
</section>

<section class="ui-section" id="scoreboard-slots">
    @include('reference._heading', ['id' => 'scoreboard-slots', 'title' => 'Markup in a figure', 'caption' => 'The value between <code>slot()</code> and <code>Ui::end()</code>, and the <code>comment</code> slot.'])
    @include('reference._example', ['view' => 'reference.ui.examples.scoreboard-slots', 'stacked' => true])
</section>

<section class="ui-section" id="scoreboard-thumbnail">
    @include('reference._heading', ['id' => 'scoreboard-thumbnail', 'title' => 'Thumbnails', 'caption' => '<code>Ui::scoreboardThumbnail()</code> shows an image as an item.'])
    @include('reference._example', ['view' => 'reference.ui.examples.scoreboard-thumbnail', 'stacked' => true])
</section>

<section class="ui-section" id="scoreboard-charts">
    @include('reference._heading', ['id' => 'scoreboard-charts', 'title' => 'Charts', 'caption' => 'A pie and a bar chart lay out as items inside a scoreboard.'])
    @include('reference._example', ['view' => 'reference.ui.examples.scoreboard-charts', 'stacked' => true])
</section>

<section class="ui-section" id="scoreboard-markup">
    @include('reference._heading', ['id' => 'scoreboard-markup', 'title' => 'Markup', 'caption' => 'The scoreboard element written by hand.'])
    @include('reference._example', ['view' => 'reference.ui.examples.scoreboard-markup', 'stacked' => true])
</section>

@endsection
