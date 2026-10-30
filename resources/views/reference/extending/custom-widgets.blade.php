@extends('layouts.app')
@section('title', 'Custom Widgets')
@section('content')

@include('reference._header', [
    'intro' => 'An app form widget with its own partials, AJAX handler, script and stylesheet.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="time-checker">
        @include('reference._heading', ['id' => 'time-checker', 'title' => 'The time checker widget', 'caption' => 'A <code>timechecker</code> field: check the server time, then save.'])
        @include('reference._form_example', ['name' => 'time-checker'])
    </section>

    @include('reference._save_bar')
</form>

<section class="ui-section" id="widget-class">
    @include('reference._heading', ['id' => 'widget-class', 'title' => 'The widget class', 'caption' => 'A <code>FormWidgetBase</code> class with an <code>onCheckTime</code> handler.'])
    <div class="ui-example">
        @include('reference._source', ['path' => 'app/FormWidgets/TimeChecker.php'])
    </div>
</section>

<section class="ui-section" id="partials">
    @include('reference._heading', ['id' => 'partials', 'title' => 'Partials', 'caption' => 'The outer partial and the inner one the handler replaces.'])
    <div class="ui-example">
        @include('reference._source', ['path' => 'app/FormWidgets/TimeChecker/partials/_timechecker.php'])
        @include('reference._source', ['path' => 'app/FormWidgets/TimeChecker/partials/_clock.php'])
    </div>
</section>

<section class="ui-section" id="registering">
    @include('reference._heading', ['id' => 'registering', 'title' => 'Registering the widget', 'caption' => '<code>registerFormWidget()</code> in a service provider.'])
    <div class="ui-example">
        @include('reference._source', ['path' => 'app/Providers/AppServiceProvider.php'])
    </div>
</section>

<section class="ui-section" id="assets">
    @include('reference._heading', ['id' => 'assets', 'title' => 'Its own assets', 'caption' => 'The script and stylesheet that <code>loadAssets()</code> adds.'])
    <div class="ui-example">
        @include('reference._source', ['path' => 'public/js/widgets/timechecker.js'])
        @include('reference._source', ['path' => 'public/css/widgets/timechecker.css'])
    </div>
</section>

<section class="ui-section" id="render-assets">
    @include('reference._heading', ['id' => 'render-assets', 'title' => 'Printing the assets', 'caption' => '<code>Ui::renderAssets()</code> in the layout head.'])
    <div class="ui-example">
        @include('reference._source', ['path' => 'resources/views/layouts/app.blade.php'])
    </div>
</section>

@endsection
