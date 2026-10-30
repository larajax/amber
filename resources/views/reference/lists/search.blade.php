@extends('layouts.app')
@section('title', 'Search')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>Search</code> widget and <code>Ui::searchInput()</code> render a search box that remembers its term.',
])

<section class="ui-section" id="standalone">
    @include('reference._heading', ['id' => 'standalone', 'title' => 'Search widget on its own', 'caption' => 'A <code>Search</code> widget answering its <code>search.submit</code> event.'])
    <div class="ui-example is-stacked">
        <div class="ui-example-preview">
            <div class="ui-narrow mb-4">
                {{ $tagSearch }}
            </div>
            <div id="tagResults">
                {!! $tagResults !!}
            </div>
        </div>
        @include('reference._method_source', ['method' => 'makeTagSearch'])
        @include('reference._method_source', ['method' => 'renderTagResults'])
    </div>
</section>

<section class="ui-section" id="bound">
    @include('reference._heading', ['id' => 'bound', 'title' => 'Bound to a list', 'caption' => 'A toolbar search box searching the <code>searchable</code> columns.'])
    @include('reference._widget_example', [
        'widgets' => [$boundToolbar, $boundList],
        'methods' => ['makeBoundToolbar', 'makeSearchedList'],
        'sources' => ['resources/ui/reference/lists/search/posts.yaml'],
    ])
</section>

<section class="ui-section" id="scope">
    @include('reference._heading', ['id' => 'scope', 'title' => 'Search scope', 'caption' => '<code>scope</code> naming the post model\'s <code>applySearch</code> scope.'])
    @include('reference._widget_example', [
        'widgets' => [$scopeToolbar, $scopeList],
        'methods' => ['makeScopeToolbar'],
    ])
</section>

<section class="ui-section" id="mode">
    @include('reference._heading', ['id' => 'mode', 'title' => 'Search mode', 'caption' => '<code>mode: any</code> matching any of the words.'])
    @include('reference._widget_example', [
        'widgets' => [$modeToolbar, $modeList],
        'methods' => ['makeModeToolbar'],
    ])
</section>

<section class="ui-section" id="search-on-enter">
    @include('reference._heading', ['id' => 'search-on-enter', 'title' => 'Search on Enter', 'caption' => '<code>searchOnEnter</code> with <code>growable: false</code>.'])
    @include('reference._widget_example', [
        'widgets' => [$enterToolbar, $enterList],
        'methods' => ['makeEnterToolbar'],
    ])
</section>

<section class="ui-section" id="search-input">
    @include('reference._heading', ['id' => 'search-input', 'title' => 'Ui::searchInput', 'caption' => 'The search box on its own, with a clear button.'])
    @include('reference._example', ['view' => 'reference.lists.examples.search-input'])
</section>

<section class="ui-section" id="search-input-ajax">
    @include('reference._heading', ['id' => 'search-input-ajax', 'title' => 'Ui::searchInput with a handler', 'caption' => '<code>handler</code> filtering a list of cities as you type.'])
    @include('reference._example', [
        'view' => 'reference.lists.examples.search-input-ajax',
        'stacked' => true,
        'related' => ['reference.lists.search._city_results' => 'The results partial'],
    ])
    <div class="ui-example">
        @include('reference._method_source', ['method' => 'onSearchCities'])
    </div>
</section>

@endsection
