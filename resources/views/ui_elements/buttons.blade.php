@extends('layouts.app')
@section('title', 'Buttons')
@section('content')

<h1 class="mb-3">
    Buttons
</h1>
<p class="fg-3 mb-5">
    The <code>Ui</code> facade renders buttons from your views with named arguments. Any argument it does not know becomes an HTML attribute, so <code>dataRequestConfirm</code> renders <code>data-request-confirm</code>.
</p>

@include('ui_elements._nav')

<section class="ui-section" id="variants">
    <h2>
        Variants
    </h2>
    <p class="fg-3">
        A styling flag picks the solid theme. Without one, the button renders Bootstrap's plain <code>btn</code> class.
    </p>
    @include('ui_elements._example', ['name' => 'button-variants'])
</section>

<section class="ui-section" id="outline">
    <h2>
        Outline, subtle and link
    </h2>
    <p class="fg-3">
        The <code>class</code> argument merges extra classes into the button, so any Bootstrap variant and theme works.
    </p>
    @include('ui_elements._example', ['name' => 'button-outline'])
</section>

<section class="ui-section" id="sizes">
    <h2>
        Sizes
    </h2>
    <p class="fg-3">
        Size classes go through <code>class</code> too: <code>btn-xs</code>, <code>btn-sm</code> and <code>btn-lg</code>.
    </p>
    @include('ui_elements._example', ['name' => 'button-sizes'])
</section>

<section class="ui-section" id="icons">
    <h2>
        With an icon
    </h2>
    <p class="fg-3">
        The <code>icon</code> argument takes icon classes, or a key from the icon pack such as <code>action.delete</code>.
    </p>
    @include('ui_elements._example', ['name' => 'button-icons'])
</section>

<section class="ui-section" id="links">
    <h2>
        Links
    </h2>
    <p class="fg-3">
        An <code>href</code> renders an anchor styled as a button.
    </p>
    @include('ui_elements._example', ['name' => 'button-links'])
</section>

<section class="ui-section" id="fluent">
    <h2>
        Fluent style
    </h2>
    <p class="fg-3">
        Methods chained on the result set the same props as named arguments. A label passed as the first argument returns the fluent element, which adds the <code>success()</code>, <code>warning()</code>, <code>outline()</code> and <code>textLink()</code> types.
    </p>
    @include('ui_elements._example', ['name' => 'button-fluent'])
</section>

<section class="ui-section" id="ajax-button">
    <h2>
        AJAX button
    </h2>
    <p class="fg-3">
        <code>Ui::ajaxButton()</code> runs a handler on the controller. <code>requestData</code> sends extra values, and <code>dataRequestConfirm</code> asks first.
    </p>
    @include('ui_elements._example', ['name' => 'ajax-buttons'])
</section>

<section class="ui-section" id="popup-button">
    <h2>
        Popup button
    </h2>
    <p class="fg-3">
        <code>Ui::popupButton()</code> opens the handler's response in a dialog, sized by <code>dataSize</code>. Elements with <code>data-dismiss="popup"</code> close it.
    </p>
    @include('ui_elements._example', [
        'name' => 'popup-buttons',
        'related' => ['ui_elements._dialog' => 'The handler renders'],
    ])
</section>

<section class="ui-section" id="icon-button">
    <h2>
        Icon button
    </h2>
    <p class="fg-3">
        <code>Ui::iconButton()</code> renders the icon alone, with the label as its title. <code>danger</code> turns it red on hover.
    </p>
    @include('ui_elements._example', ['name' => 'icon-buttons'])
</section>

<section class="ui-section" id="dropdown-button">
    <h2>
        Dropdown button
    </h2>
    <p class="fg-3">
        <code>Ui::dropdownButton()</code> captures its items between <code>slot()</code> and <code>Ui::end()</code>. Items run a handler or follow a link, and <code>caret: false</code> hides the arrow.
    </p>
    @include('ui_elements._example', ['name' => 'dropdown-buttons'])
</section>

<section class="ui-section" id="hotkeys">
    <h2>
        Hotkeys
    </h2>
    <p class="fg-3">
        A <code>hotkey</code> clicks the button from the keyboard, and hovering shows the shortcut in a tooltip. Try <kbd>Ctrl</kbd> + <kbd>S</kbd>.
    </p>
    @include('ui_elements._example', ['name' => 'hotkey-buttons'])
</section>

@endsection
