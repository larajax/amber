@extends('layouts.app')
@section('title', 'List Structure')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>ListStructure</code> widget is a list that shows parent and child records as a tree and reorders them by dragging. It takes the options of a list, adds its own, and saves each move to the model: a nested set moves the node, and a sortable model stores the new order.',
])

<section class="ui-section" id="tree">
    <h2>
        Tree
    </h2>
    <p class="fg-3">
        The categories use the <code>NestedTree</code> trait. Drag a row up or down to reorder it, or to the right to nest it under the row above, down to <code>maxDepth</code> levels. The arrows expand and collapse a branch, and the list remembers them for the session. Sorting by a header or searching shows a flat list, and the icon at the end of the header, or a third click on the header, brings the tree back.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$treeToolbar, $treeList],
        'methods' => ['makeTreeList', 'makeTreeToolbar'],
        'sources' => ['resources/ui/reference/lists/list-structure/categories.yaml'],
    ])
</section>

<section class="ui-section" id="browse">
    <h2>
        Browsing tree
    </h2>
    <p class="fg-3">
        <code>treeExpanded: false</code> starts with every branch closed, and <code>showReorder: false</code> removes the drag handles, leaving a tree to browse. <code>permissions</code> does the same for visitors without one of the named gate abilities.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$browseTree],
        'methods' => ['makeBrowseTree'],
    ])
</section>

<section class="ui-section" id="reorder">
    <h2>
        Reorderable list
    </h2>
    <p class="fg-3">
        <code>showTree: false</code> reorders a flat list, for a model that implements <code>SortableInterface</code>, like the users here. After a drop, the list posts the new order of every row as <code>sort_orders</code> to the model's <code>setSortableOrder()</code>. <code>dragRow: false</code> drags by the handle only, so the rows can still be selected and clicked.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$reorderList],
        'methods' => ['makeReorderList'],
        'sources' => ['resources/ui/reference/lists/list-structure/users.yaml'],
        'previewClass' => 'reference-scroll-preview',
    ])
</section>

@endsection
