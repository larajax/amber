@extends('layouts.app')
@section('title', 'Popups')
@section('content')

@include('reference._header', [
    'intro' => 'Modal dialogs on a native <code>&lt;dialog&gt;</code>, popovers beside an element, and hover tooltips.',
])

<section class="ui-section" id="popup-button">
    @include('reference._heading', ['id' => 'popup-button', 'title' => 'Popup button', 'caption' => '<code>Ui::popupButton()</code> in the default and fixed <code>dataSize</code> widths.'])
    @include('reference._example', [
        'view' => 'reference.ui.examples.popup-buttons',
        'related' => ['reference.ui._dialog' => 'The handler renders'],
    ])
</section>

<section class="ui-section" id="window-sizes">
    @include('reference._heading', ['id' => 'window-sizes', 'title' => 'Sizes from the window and content', 'caption' => '<code>adaptive</code>, <code>dataAdaptiveHeight</code>, <code>auto</code>, pixels and <code>fullscreen</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.popup-window-sizes'])
</section>

<section class="ui-section" id="closing">
    @include('reference._heading', ['id' => 'closing', 'title' => 'Closing', 'caption' => '<code>dataKeyboard: \'false\'</code> and <code>dataBackdrop: true</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.popup-closing'])
</section>

<section class="ui-section" id="popup-markup">
    @include('reference._heading', ['id' => 'popup-markup', 'title' => 'Popup control in markup', 'caption' => '<code>data-control="popup"</code> on a link, with <code>data-request-data</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.popup-markup'])
</section>

<section class="ui-section" id="popup-javascript">
    @include('reference._heading', ['id' => 'popup-javascript', 'title' => 'Popups from JavaScript', 'caption' => '<code>jax.popup.open()</code> with <code>content</code> from a template.'])
    @include('reference._example', ['view' => 'reference.ui.examples.popup-javascript'])
</section>

<section class="ui-section" id="popover">
    @include('reference._heading', ['id' => 'popover', 'title' => 'Popover', 'caption' => '<code>data-control="popover"</code> with each <code>data-placement</code> and <code>data-modal</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.popover'])
</section>

<section class="ui-section" id="tooltip">
    @include('reference._heading', ['id' => 'tooltip', 'title' => 'Tooltip', 'caption' => '<code>data-tooltip-text</code>, <code>data-tooltip-position</code> and <code>data-tooltip-hotkey</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.tooltip'])
</section>

@endsection
