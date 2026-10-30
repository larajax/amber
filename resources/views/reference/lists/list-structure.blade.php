@extends('layouts.app')
@section('title', 'List Structure')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>ListStructure</code> widget shows records as a tree, or a flat list, reordered by dragging.',
])

<section class="ui-section" id="tree">
    @include('reference._heading', ['id' => 'tree', 'title' => 'Tree', 'caption' => 'A nested tree of categories with <code>maxDepth: 3</code>.'])
    @include('reference._widget_example', [
        'widgets' => [$treeToolbar, $treeList],
        'methods' => ['makeTreeList', 'makeTreeToolbar'],
        'sources' => ['resources/ui/reference/lists/list-structure/categories.yaml'],
    ])
</section>

<section class="ui-section" id="browse">
    @include('reference._heading', ['id' => 'browse', 'title' => 'Browsing tree', 'caption' => '<code>treeExpanded: false</code> and <code>showReorder: false</code>.'])
    @include('reference._widget_example', [
        'widgets' => [$browseTree],
        'methods' => ['makeBrowseTree'],
    ])
</section>

<section class="ui-section" id="reorder">
    @include('reference._heading', ['id' => 'reorder', 'title' => 'Reorderable list', 'caption' => '<code>showTree: false</code> and <code>dragRow: false</code> on a sortable model.'])
    @include('reference._widget_example', [
        'widgets' => [$reorderList],
        'methods' => ['makeReorderList'],
        'sources' => ['resources/ui/reference/lists/list-structure/users.yaml'],
        'previewClass' => 'reference-scroll-preview',
    ])
</section>

@endsection
