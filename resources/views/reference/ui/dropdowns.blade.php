@extends('layouts.app')
@section('title', 'Dropdowns')
@section('content')

@include('reference._header', [
    'intro' => 'Bootstrap 6 menus with the library look, from <code>Ui::dropdownButton()</code> or plain markup.',
])

<section class="ui-section" id="dropdown-button">
    @include('reference._heading', ['id' => 'dropdown-button', 'title' => 'Dropdown button', 'caption' => '<code>Ui::dropdownButton()</code> with <code>Ui::dropdownItem()</code> and <code>Ui::dropdownDivider()</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.dropdown-buttons'])
</section>

<section class="ui-section" id="dropdown-caret">
    @include('reference._heading', ['id' => 'dropdown-caret', 'title' => 'Icon and caret', 'caption' => '<code>icon</code> with <code>caret: false</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.dropdown-caret'])
</section>

<section class="ui-section" id="dropdown-items">
    @include('reference._heading', ['id' => 'dropdown-items', 'title' => 'Item options', 'caption' => 'A confirmed item, a <code>disabled</code> item and <code>theme-danger</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.dropdown-items'])
</section>

<section class="ui-section" id="dropdown-headers">
    @include('reference._heading', ['id' => 'dropdown-headers', 'title' => 'Headers, checks and hotkeys', 'caption' => '<code>Ui::dropdownHeader()</code>, a <code>selected</code> item, a <code>hotkey</code> and a <code>popup</code> item.'])
    @include('reference._example', ['view' => 'reference.ui.examples.dropdown-headers'])
</section>

<section class="ui-section" id="dropdown-submenus">
    @include('reference._heading', ['id' => 'dropdown-submenus', 'title' => 'Submenus', 'caption' => '<code>Ui::dropdownItem()</code> with a slot of items.'])
    @include('reference._example', ['view' => 'reference.ui.examples.dropdown-submenus'])
</section>

<section class="ui-section" id="split-button">
    @include('reference._heading', ['id' => 'split-button', 'title' => 'Split button', 'caption' => '<code>Ui::splitButton()</code>, a button and a caret that opens a menu.'])
    @include('reference._example', ['view' => 'reference.ui.examples.dropdown-split'])
</section>

<section class="ui-section" id="dropdown-markup">
    @include('reference._heading', ['id' => 'dropdown-markup', 'title' => 'Menus in markup', 'caption' => 'A <code>data-bs-toggle="menu"</code> toggle and a <code>control-dropdown</code> menu.'])
    @include('reference._example', ['view' => 'reference.ui.examples.dropdown-markup'])
</section>

<section class="ui-section" id="dropdown-placement">
    @include('reference._heading', ['id' => 'dropdown-placement', 'title' => 'Placement', 'caption' => '<code>data-bs-placement</code> set to <code>top-start</code> and <code>bottom-end</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.dropdown-placement'])
</section>

@endsection
