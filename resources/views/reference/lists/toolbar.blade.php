@extends('layouts.app')
@section('title', 'Toolbar')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>Toolbar</code> widget is the bar of buttons, search box and setup icon above a list.',
])

<section class="ui-section" id="yaml-buttons">
    @include('reference._heading', ['id' => 'yaml-buttons', 'title' => 'Buttons from YAML', 'caption' => 'Link, popup, AJAX and <code>checked</code> buttons declared in YAML.'])
    @include('reference._widget_example', [
        'widgets' => [$buttonsToolbar, $buttonsList],
        'methods' => ['makeButtonsToolbar', 'onCountCheckedPosts'],
        'sources' => ['resources/ui/reference/lists/toolbar/buttons.yaml'],
    ])
</section>

<section class="ui-section" id="partial">
    @include('reference._heading', ['id' => 'partial', 'title' => 'Buttons from a partial', 'caption' => 'A partial of <code>Ui</code> buttons using the <code>data-list-checked-*</code> attributes.'])
    @include('reference._widget_example', [
        'widgets' => [$partialToolbar, $partialList],
        'methods' => ['makePartialToolbar'],
        'sources' => ['resources/views/reference/lists/toolbar/_list_toolbar.php'],
    ])
</section>

<section class="ui-section" id="search">
    @include('reference._heading', ['id' => 'search', 'title' => 'Search box and list setup', 'caption' => 'The <code>search</code> and <code>setupHandler</code> options.'])
    @include('reference._widget_example', [
        'widgets' => [$searchToolbar, $searchList],
        'methods' => ['makeSearchToolbar', 'makeToolbarList'],
        'sources' => ['resources/ui/reference/lists/toolbar/columns.yaml'],
    ])
</section>

<section class="ui-section" id="overflow">
    @include('reference._heading', ['id' => 'overflow', 'title' => 'Overflowing buttons', 'caption' => 'More buttons than fit, each sending <code>requestData</code>.'])
    @include('reference._widget_example', [
        'widgets' => [$overflowToolbar],
        'methods' => ['makeOverflowToolbar', 'onToolbarButton'],
        'sources' => ['resources/ui/reference/lists/toolbar/overflow.yaml'],
        'previewClass' => 'reference-toolbar-narrow',
    ])
</section>

<section class="ui-section" id="drag-scroll">
    @include('reference._heading', ['id' => 'drag-scroll', 'title' => 'Drag scroll', 'caption' => 'The <code>drag-scroll</code> control on a row of badges.'])
    @include('reference._example', [
        'view' => 'reference.lists.examples.drag-scroll',
        'stacked' => true,
    ])
</section>

@endsection
