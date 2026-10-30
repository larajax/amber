@extends('layouts.app')
@section('title', 'Custom Widgets')
@section('content')

@include('reference._header', [
    'intro' => 'An app can add its own form widgets beside the built-in ones. The time checker below is one: a PHP class with its own partials, an AJAX handler, and a script and stylesheet that load only on pages that render it.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="time-checker">
        <h2>
            The time checker widget
        </h2>
        <p class="fg-3">
            It compares the browser clock with the server clock. Checking runs the widget's own <code>onCheckTime</code> handler, which stamps the server time into the field, and Save stores it like any other field.
        </p>
        @include('reference._form_example', ['name' => 'time-checker'])
    </section>

    @include('reference._save_bar')
</form>

<section class="ui-section" id="widget-class">
    <h2>
        The widget class
    </h2>
    <p class="fg-3">
        A form widget extends <code>FormWidgetBase</code>. <code>init()</code> reads its options from the field, <code>render()</code> returns its markup, <code>getSaveValue()</code> shapes the posted value, and public <code>on*</code> methods are AJAX handlers, called as <code>alias::onCheckTime</code>.
    </p>
    <div class="ui-example">
        @include('reference._source', ['path' => 'app/FormWidgets/TimeChecker.php'])
    </div>
</section>

<section class="ui-section" id="partials">
    <h2>
        Partials
    </h2>
    <p class="fg-3">
        <code>makePartial()</code> renders files from the <code>partials</code> folder beside the class. The outer partial keeps the element the handler replaces, and the inner one is what it replaces it with.
    </p>
    <div class="ui-example">
        @include('reference._source', ['path' => 'app/FormWidgets/TimeChecker/partials/_timechecker.php'])
        @include('reference._source', ['path' => 'app/FormWidgets/TimeChecker/partials/_clock.php'])
    </div>
</section>

<section class="ui-section" id="registering">
    <h2>
        Registering the widget
    </h2>
    <p class="fg-3">
        <code>registerFormWidget()</code> gives the class a code, which fields name as their <code>type</code>.
    </p>
    <div class="ui-example">
        @include('reference._source', ['path' => 'app/Providers/AppServiceProvider.php'])
    </div>
</section>

<section class="ui-section" id="assets">
    <h2>
        Its own assets
    </h2>
    <p class="fg-3">
        <code>loadAssets()</code> runs when the widget is created. <code>addCss()</code> and <code>addJs()</code> take a path under <code>public</code>, or under the widget's <code>assetPath</code> when it sets one, or a full URL, plus attributes for the tag such as <code>['defer' => true]</code> or <code>['type' => 'module']</code>. The script registers a control that the markup starts with <code>data-control="timechecker"</code>.
    </p>
    <div class="ui-example">
        @include('reference._source', ['path' => 'public/js/widgets/timechecker.js'])
        @include('reference._source', ['path' => 'public/css/widgets/timechecker.css'])
    </div>
</section>

<section class="ui-section" id="render-assets">
    <h2>
        Printing the assets
    </h2>
    <p class="fg-3">
        <code>Ui::renderAssets()</code> prints a tag for every asset the widgets added during the request, stylesheets first and each one once. The layout calls it in the head, which works because the page content renders before the layout. Widgets created by an AJAX handler send their assets with the response instead, and the browser loads any it does not have yet.
    </p>
    <div class="ui-example">
        @include('reference._source', ['path' => 'resources/views/layouts/app.blade.php'])
    </div>
</section>

@endsection
