@extends('layouts.app')
@section('title', 'Flags')
@section('content')

@include('reference._header', [
    'intro' => 'The flag element shows a country\'s flag from a <code>flag-</code> class with its two letter code.',
])

<section class="ui-section" id="flags-countries">
    @include('reference._heading', ['id' => 'flags-countries', 'title' => 'Country flags', 'caption' => 'A <code>flag-au</code> class for each country.'])
    @include('reference._example', ['view' => 'reference.ui.examples.flags-countries'])
</section>

<section class="ui-section" id="flags-sizing">
    @include('reference._heading', ['id' => 'flags-sizing', 'title' => 'Sized by the text', 'caption' => 'A flag is as tall as the text around it.'])
    @include('reference._example', ['view' => 'reference.ui.examples.flags-sizing'])
</section>

<section class="ui-section" id="flags-special">
    @include('reference._heading', ['id' => 'flags-special', 'title' => 'More flags', 'caption' => 'The European Union, the United Nations and the nations of the United Kingdom.'])
    @include('reference._example', ['view' => 'reference.ui.examples.flags-special'])
</section>

@endsection
