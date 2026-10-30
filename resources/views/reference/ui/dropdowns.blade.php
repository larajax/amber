@extends('layouts.app')
@section('title', 'Dropdowns')
@section('content')

@include('reference._header', [
    'intro' => 'Dropdown menus are Bootstrap 6 menus, opened by Bootstrap\'s own <code>data-bs-toggle="menu"</code>. The <code>dropdown</code> control only styles them, and <code>Ui::dropdownButton()</code> renders the toggle and the menu together.',
])

<section class="ui-section" id="dropdown-button">
    <h2>
        Dropdown button
    </h2>
    <p class="fg-3">
        <code>Ui::dropdownButton()</code> captures its items between <code>slot()</code> and <code>Ui::end()</code>. <code>Ui::dropdownItem()</code> runs a handler or follows a link, and <code>Ui::dropdownDivider()</code> separates groups.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.dropdown-buttons'])
</section>

<section class="ui-section" id="dropdown-caret">
    <h2>
        Icon and caret
    </h2>
    <p class="fg-3">
        The <code>icon</code> argument shows an icon before the label, and <code>caret: false</code> hides the arrow.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.dropdown-caret'])
</section>

<section class="ui-section" id="dropdown-items">
    <h2>
        Item options
    </h2>
    <p class="fg-3">
        Items take any attribute, so they can ask for confirmation or send request data. The <code>class</code> argument merges into <code>menu-item</code>: <code>disabled</code> greys an item out, and a theme such as <code>theme-danger</code> colors a destructive one, hover included.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.dropdown-items'])
</section>

<section class="ui-section" id="dropdown-markup">
    <h2>
        Menus in markup
    </h2>
    <p class="fg-3">
        Any toggle with <code>data-bs-toggle="menu"</code> opens the <code>menu</code> beside it. Add <code>control-dropdown</code> to the menu for the library look, and keep the items as its direct children so the arrow keys reach them.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.dropdown-markup'])
</section>

<section class="ui-section" id="dropdown-placement">
    <h2>
        Placement
    </h2>
    <p class="fg-3">
        <code>data-bs-placement</code> on the toggle picks the side and alignment, such as <code>top-start</code>, or <code>bottom-end</code> to line the menu up with the toggle's right edge.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.dropdown-placement'])
</section>

@endsection
