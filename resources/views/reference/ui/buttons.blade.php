@extends('layouts.app')
@section('title', 'Buttons')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>Ui</code> facade renders buttons from named arguments, and any other argument becomes an attribute.',
])

<section class="ui-section" id="variants">
    @include('reference._heading', ['id' => 'variants', 'title' => 'Variants', 'caption' => '<code>primary</code>, <code>secondary</code>, <code>danger</code> and no flag.'])
    @include('reference._example', ['view' => 'reference.ui.examples.button-variants'])
</section>

<section class="ui-section" id="outline">
    @include('reference._heading', ['id' => 'outline', 'title' => 'Outline, subtle and link', 'caption' => 'Bootstrap variants and themes through <code>class</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.button-outline'])
</section>

<section class="ui-section" id="sizes">
    @include('reference._heading', ['id' => 'sizes', 'title' => 'Sizes', 'caption' => '<code>btn-xs</code>, <code>btn-sm</code> and <code>btn-lg</code> through <code>class</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.button-sizes'])
</section>

<section class="ui-section" id="icons">
    @include('reference._heading', ['id' => 'icons', 'title' => 'With an icon', 'caption' => '<code>icon</code> with icon classes or an icon pack key.'])
    @include('reference._example', ['view' => 'reference.ui.examples.button-icons'])
</section>

<section class="ui-section" id="links">
    @include('reference._heading', ['id' => 'links', 'title' => 'Links', 'caption' => '<code>href</code> renders an anchor styled as a button.'])
    @include('reference._example', ['view' => 'reference.ui.examples.button-links'])
</section>

<section class="ui-section" id="attributes">
    @include('reference._heading', ['id' => 'attributes', 'title' => 'Attributes', 'caption' => 'Unknown arguments as attributes: <code>dataRequestFlash</code>, <code>title</code>, <code>disabled</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.button-attributes'])
</section>

<section class="ui-section" id="groups">
    @include('reference._heading', ['id' => 'groups', 'title' => 'Groups and text', 'caption' => 'A <code>btn-group</code> bar and <code>button-text</code> beside a button.'])
    @include('reference._example', ['view' => 'reference.ui.examples.button-groups'])
</section>

<section class="ui-section" id="ajax-button">
    @include('reference._heading', ['id' => 'ajax-button', 'title' => 'AJAX button', 'caption' => '<code>Ui::ajaxButton()</code> with <code>requestData</code> and <code>dataRequestConfirm</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.ajax-buttons'])
</section>

<section class="ui-section" id="loading">
    @include('reference._heading', ['id' => 'loading', 'title' => 'Loading state', 'caption' => '<code>dataAttachLoading</code> on a slow request.'])
    @include('reference._example', ['view' => 'reference.ui.examples.button-loading'])
</section>

<section class="ui-section" id="icon-button">
    @include('reference._heading', ['id' => 'icon-button', 'title' => 'Icon button', 'caption' => '<code>Ui::iconButton()</code>, plain and with <code>danger</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.icon-buttons'])
</section>

<section class="ui-section" id="hotkeys">
    @include('reference._heading', ['id' => 'hotkeys', 'title' => 'Hotkeys', 'caption' => '<code>hotkey</code> on three button kinds. Try <kbd>Ctrl</kbd> + <kbd>S</kbd>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.hotkey-buttons'])
</section>

<section class="ui-section" id="fluent">
    @include('reference._heading', ['id' => 'fluent', 'title' => 'Fluent props', 'caption' => 'Chained <code>primary()</code>, <code>icon()</code>, <code>handler()</code> and <code>requestData()</code>.'])
    @include('reference._example', ['view' => 'reference.ui.examples.button-fluent'])
</section>

@endsection
