@extends('layouts.app')
@section('title', 'Buttons')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>Ui</code> facade renders buttons from your views with named arguments. Any argument it does not know becomes an HTML attribute, so <code>dataRequestConfirm</code> renders <code>data-request-confirm</code>.',
])

<section class="ui-section" id="variants">
    <h2>
        Variants
    </h2>
    <p class="fg-3">
        A styling flag picks the solid theme. Without one, the button renders Bootstrap's plain <code>btn</code> class.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.button-variants'])
</section>

<section class="ui-section" id="outline">
    <h2>
        Outline, subtle and link
    </h2>
    <p class="fg-3">
        The <code>class</code> argument merges extra classes into the button, so any Bootstrap variant and theme works.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.button-outline'])
</section>

<section class="ui-section" id="sizes">
    <h2>
        Sizes
    </h2>
    <p class="fg-3">
        Size classes go through <code>class</code> too: <code>btn-xs</code>, <code>btn-sm</code> and <code>btn-lg</code>.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.button-sizes'])
</section>

<section class="ui-section" id="icons">
    <h2>
        With an icon
    </h2>
    <p class="fg-3">
        The <code>icon</code> argument takes icon classes, or a key from the icon pack such as <code>action.delete</code>.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.button-icons'])
</section>

<section class="ui-section" id="links">
    <h2>
        Links
    </h2>
    <p class="fg-3">
        An <code>href</code> renders an anchor styled as a button.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.button-links'])
</section>

<section class="ui-section" id="attributes">
    <h2>
        Attributes
    </h2>
    <p class="fg-3">
        Arguments in camel case render in kebab case. A data attribute set to <code>true</code> renders as <code>"true"</code>, which the framework reads as on, and <code>false</code> leaves it out. Other attributes set to <code>true</code>, such as <code>disabled</code>, repeat their name.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.button-attributes'])
</section>

<section class="ui-section" id="groups">
    <h2>
        Groups and text
    </h2>
    <p class="fg-3">
        A <code>btn-group</code> joins buttons into one bar. A <code>button-text</code> span sets muted text beside a button, such as a cancel link.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.button-groups'])
</section>

<section class="ui-section" id="ajax-button">
    <h2>
        AJAX button
    </h2>
    <p class="fg-3">
        <code>Ui::ajaxButton()</code> runs a handler on the controller. <code>requestData</code> sends extra values, and <code>dataRequestConfirm</code> asks first.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.ajax-buttons'])
</section>

<section class="ui-section" id="loading">
    <h2>
        Loading state
    </h2>
    <p class="fg-3">
        <code>dataAttachLoading</code> disables the button and adds a spinner while its request runs.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.button-loading'])
</section>

<section class="ui-section" id="icon-button">
    <h2>
        Icon button
    </h2>
    <p class="fg-3">
        <code>Ui::iconButton()</code> renders the icon alone, with the label as its title. <code>danger</code> turns it red on hover.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.icon-buttons'])
</section>

<section class="ui-section" id="hotkeys">
    <h2>
        Hotkeys
    </h2>
    <p class="fg-3">
        A <code>hotkey</code> clicks the button from the keyboard, and hovering shows the shortcut in a tooltip. Try <kbd>Ctrl</kbd> + <kbd>S</kbd>.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.hotkey-buttons'])
</section>

<section class="ui-section" id="fluent">
    <h2>
        Fluent props
    </h2>
    <p class="fg-3">
        Methods chained on the result set the same props as the named arguments, such as <code>primary()</code>, <code>icon()</code>, <code>handler()</code> and <code>requestData()</code>. Attributes still go in the named arguments.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.button-fluent'])
</section>

<section class="ui-section" id="fluent-elements">
    <h2>
        Fluent elements
    </h2>
    <p class="fg-3">
        A label passed as the first argument returns a fluent element instead. It adds the <code>success()</code>, <code>warning()</code> and <code>info()</code> types, <code>outline()</code>, <code>textLink()</code>, <code>circleIcon()</code>, and <code>linkTo()</code>, which resolves a path through <code>Ui::url()</code>.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.button-fluent-elements'])
</section>

<section class="ui-section" id="fluent-ajax">
    <h2>
        Fluent AJAX and popup buttons
    </h2>
    <p class="fg-3">
        With a label and a handler, <code>Ui::ajaxButton()</code> and <code>Ui::popupButton()</code> return fluent elements with <code>ajaxData()</code>, <code>confirmMessage()</code>, <code>attachLoading()</code> and, for popups, <code>size()</code>. <code>formDeleteButton()</code> turns the button into the trash icon that forms show beside Save.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.button-fluent-ajax'])
</section>

@endsection
