@extends('layouts.app')
@section('title', 'Popups')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>popup</code> control opens a modal dialog on a native <code>&lt;dialog&gt;</code>, usually with markup returned by a handler. Popovers open a panel beside an element, and tooltips show a short hint on hover.',
])

<section class="ui-section" id="popup-button">
    <h2>
        Popup button
    </h2>
    <p class="fg-3">
        <code>Ui::popupButton()</code> opens the handler's response in a dialog, sized by <code>dataSize</code>. Elements with <code>data-dismiss="popup"</code> close it, and a popup opened from inside another stacks over it, as the dialog's Open another button shows.
    </p>
    @include('reference._example', [
        'view' => 'reference.ui.examples.popup-buttons',
        'related' => ['reference.ui._dialog' => 'The handler renders'],
    ])
</section>

<section class="ui-section" id="window-sizes">
    <h2>
        Sizes from the window and content
    </h2>
    <p class="fg-3">
        <code>adaptive</code> fills the window less a margin, <code>auto</code> fits the content, a number sets the width in pixels and <code>fullscreen</code> covers the window. <code>dataAdaptiveHeight</code> stretches any size to the window height.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.popup-window-sizes'])
</section>

<section class="ui-section" id="closing">
    <h2>
        Closing
    </h2>
    <p class="fg-3">
        Escape closes a popup and a click on the backdrop shakes it. <code>dataKeyboard: 'false'</code> makes Escape shake it too, and <code>dataBackdrop: true</code> lets a backdrop click close it.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.popup-closing'])
</section>

<section class="ui-section" id="popup-markup">
    <h2>
        Popup control in markup
    </h2>
    <p class="fg-3">
        <code>data-control="popup"</code> works on any element. <code>data-handler</code> names the handler, and <code>data-request-data</code> sends extra values with it.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.popup-markup'])
</section>

<section class="ui-section" id="popup-javascript">
    <h2>
        Popups from JavaScript
    </h2>
    <p class="fg-3">
        <code>jax.popup.open()</code> takes the same options as the attributes, and <code>content</code> places markup in the dialog without a request.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.popup-javascript'])
</section>

<section class="ui-section" id="popover">
    <h2>
        Popover
    </h2>
    <p class="fg-3">
        <code>data-control="popover"</code> opens a panel next to the element. <code>data-content-from</code> copies the content from a template, <code>data-placement</code> picks the side, and <code>data-modal</code> dims the page behind it.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.popover'])
</section>

<section class="ui-section" id="tooltip">
    <h2>
        Tooltip
    </h2>
    <p class="fg-3">
        Any element with <code>data-tooltip-text</code> shows a tooltip after a short hover, below it unless <code>data-tooltip-position="top"</code>. <code>data-tooltip-hotkey</code> adds key labels, which is how buttons with a <code>hotkey</code> show their shortcut.
    </p>
    @include('reference._example', ['view' => 'reference.ui.examples.tooltip'])
</section>

@endsection
