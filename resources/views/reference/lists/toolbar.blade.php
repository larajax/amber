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

<section class="ui-section" id="menus">
    @include('reference._heading', ['id' => 'menus', 'title' => 'Dropdown menus', 'caption' => '<code>items</code> with a <code>header</code>, dividers, a submenu, a <code>hotkey</code> and an <code>active</code> item.'])
    @include('reference._widget_example', [
        'widgets' => [$menusToolbar, $menusList],
        'methods' => ['makeMenusToolbar', 'onCheckedToolbarButton'],
        'sources' => ['resources/ui/reference/lists/toolbar/menus.yaml'],
    ])
</section>

<section class="ui-section" id="split-buttons">
    @include('reference._heading', ['id' => 'split-buttons', 'title' => 'Split buttons and single rows', 'caption' => 'A button with <code>items</code> and its own action, and <code>checked: single</code>.'])
    @include('reference._widget_example', [
        'widgets' => [$splitToolbar, $splitList],
        'methods' => ['makeSplitToolbar', 'onShowCheckedPost'],
        'sources' => ['resources/ui/reference/lists/toolbar/split.yaml'],
    ])
</section>

<section class="ui-section" id="groups-alignment">
    @include('reference._heading', ['id' => 'groups-alignment', 'title' => 'Groups and alignment', 'caption' => '<code>group</code>, <code>active</code>, <code>divider</code>, <code>disabled</code>, <code>partial</code> and <code>align: end</code>.'])
    @include('reference._widget_example', [
        'widgets' => [$groupedToolbar],
        'methods' => ['makeGroupedToolbar', 'isToolbarViewActive'],
        'sources' => ['resources/ui/reference/lists/toolbar/grouped.yaml', 'resources/views/reference/lists/toolbar/_toolbar_status.php'],
    ])
</section>

<section class="ui-section" id="extend-buttons">
    @include('reference._heading', ['id' => 'extend-buttons', 'title' => 'Extending buttons', 'caption' => '<code>addButtons()</code> with <code>before</code> and <code>after</code>, and dotted names for menu items.'])
    @include('reference._widget_example', [
        'widgets' => [$extendedToolbar],
        'methods' => ['makeExtendedToolbar'],
        'sources' => ['resources/ui/reference/lists/toolbar/extend.yaml'],
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
