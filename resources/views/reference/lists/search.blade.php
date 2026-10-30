@extends('layouts.app')
@section('title', 'Search')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>Search</code> widget is a search box that remembers its term for the session and fires <code>search.submit</code> when the term changes. A toolbar builds one from its <code>search</code> option and binds it to a list. <code>Ui::searchInput()</code> renders the same box for any AJAX handler.',
])

<section class="ui-section" id="standalone">
    <h2>
        Search widget on its own
    </h2>
    <p class="fg-3">
        The widget runs its <code>onSubmit</code> handler as you type, and whatever the <code>search.submit</code> listeners return goes back as the response, here the tags matching the term. <code>growable: false</code> keeps the box at its full width, and the term is still there after a reload.
    </p>
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
    <h2>
        Bound to a list
    </h2>
    <p class="fg-3">
        A toolbar's search box searches the list it is bound to, looking in the columns marked <code>searchable</code>. Every word must match, in any of those columns. The box grows when it has focus, and the clear button empties it.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$boundToolbar, $boundList],
        'methods' => ['makeBoundToolbar', 'makeSearchedList'],
        'sources' => ['resources/ui/reference/lists/search/posts.yaml'],
    ])
</section>

<section class="ui-section" id="scope">
    <h2>
        Search scope
    </h2>
    <p class="fg-3">
        <code>scope</code> names a model scope that runs the search in place of the searchable columns, receiving the term and those columns. The post model's <code>scopeApplySearch()</code> also matches category and tag names, so "frontend" finds the posts in the Frontend category and "javascript" the posts with that tag.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$scopeToolbar, $scopeList],
        'methods' => ['makeScopeToolbar'],
    ])
</section>

<section class="ui-section" id="mode">
    <h2>
        Search mode
    </h2>
    <p class="fg-3">
        <code>mode</code> decides how the words match: <code>all</code>, the default, needs every word, <code>any</code> needs one of them, and <code>exact</code> looks for the whole phrase. Try "dark lists" here, which matches posts with either word.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$modeToolbar, $modeList],
        'methods' => ['makeModeToolbar'],
    ])
</section>

<section class="ui-section" id="search-on-enter">
    <h2>
        Search on Enter
    </h2>
    <p class="fg-3">
        <code>searchOnEnter</code> waits for Enter before searching, for searches that are slow to run. <code>growable: false</code> keeps the box at one width. With <code>partial</code>, the search renders a partial of the controller in place of its own box.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$enterToolbar, $enterList],
        'methods' => ['makeEnterToolbar'],
    ])
</section>

<section class="ui-section" id="search-input">
    <h2>
        Ui::searchInput
    </h2>
    <p class="fg-3">
        The <code>search-input</code> control gives the box its clear button, which shows once there is text. The other arguments, such as the placeholder, go on the input.
    </p>
    @include('reference._example', ['view' => 'reference.lists.examples.search-input'])
</section>

<section class="ui-section" id="search-input-ajax">
    <h2>
        Ui::searchInput with a handler
    </h2>
    <p class="fg-3">
        With <code>handler</code>, the input runs that handler half a second after typing stops and shows a loading indicator while it runs. Clearing the input runs it again. Here the handler filters a list of cities.
    </p>
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
