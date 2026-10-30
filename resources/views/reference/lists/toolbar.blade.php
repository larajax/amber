@extends('layouts.app')
@section('title', 'Toolbar')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>Toolbar</code> widget is the bar above a list, holding its buttons, a search box and the list setup icon. <code>bindToListWidget()</code> links the toolbar to a list, so buttons can follow and send its checked rows and the search box searches it. The handlers on this page only report what they would do.',
])

<section class="ui-section" id="yaml-buttons">
    <h2>
        Buttons from YAML
    </h2>
    <p class="fg-3">
        <code>buttons</code> takes a YAML file or an array of buttons. A button with a <code>url</code> is a link, and one with a <code>handler</code> runs an AJAX request, or opens a popup with <code>popup: true</code>. <code>type</code> picks the style, <code>confirm</code> asks first, <code>hotkey</code> binds a key, here Shift+P, and <code>attributes</code> adds attributes to the button. <code>checked: true</code> enables the button while rows are checked and sends their ids as <code>checked</code>.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$buttonsToolbar, $buttonsList],
        'methods' => ['makeButtonsToolbar', 'onCountCheckedPosts'],
        'sources' => ['resources/ui/reference/lists/toolbar/buttons.yaml'],
    ])
</section>

<section class="ui-section" id="partial">
    <h2>
        Buttons from a partial
    </h2>
    <p class="fg-3">
        Given any other path, <code>buttons</code> renders that partial, so the buttons can be anything the <code>Ui</code> factory makes: a popup button with a hotkey, here Shift+Q, and a dropdown of bulk actions. <code>data-list-checked-trigger</code> enables an element while rows are checked, <code>data-list-checked-request</code> sends the checked rows with its request, and an element with <code>data-list-checked-counter</code> inside it shows how many are checked.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$partialToolbar, $partialList],
        'methods' => ['makePartialToolbar'],
        'sources' => ['resources/views/reference/lists/toolbar/_list_toolbar.php'],
    ])
</section>

<section class="ui-section" id="search">
    <h2>
        Search box and list setup
    </h2>
    <p class="fg-3">
        <code>search</code> adds a search box that searches the bound list as you type, using the options of the <code>Search</code> widget. <code>setupHandler</code> adds the icon that opens the list setup, so the visitor can choose the columns, here including the hidden excerpt, and the page size.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$searchToolbar, $searchList],
        'methods' => ['makeSearchToolbar', 'makeToolbarList'],
        'sources' => ['resources/ui/reference/lists/toolbar/columns.yaml'],
    ])
</section>

<section class="ui-section" id="overflow">
    <h2>
        Overflowing buttons
    </h2>
    <p class="fg-3">
        The <code>toolbar</code> control on the button container keeps the buttons on one line. When they do not fit, drag them sideways or use the mouse wheel to scroll, and a shadow marks the side with more buttons. Each button here sends its name with <code>requestData</code>.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$overflowToolbar],
        'methods' => ['makeOverflowToolbar', 'onToolbarButton'],
        'sources' => ['resources/ui/reference/lists/toolbar/overflow.yaml'],
        'previewClass' => 'reference-toolbar-narrow',
    ])
</section>

<section class="ui-section" id="drag-scroll">
    <h2>
        Drag scroll
    </h2>
    <p class="fg-3">
        The <code>drag-scroll</code> control, which the toolbar builds on, scrolls any row that is wider than its container by dragging or with the wheel. It adds <code>scroll-before</code> and <code>scroll-after</code> classes to the parent, which this page styles as fades.
    </p>
    @include('reference._example', [
        'view' => 'reference.lists.examples.drag-scroll',
        'stacked' => true,
    ])
</section>

@endsection
