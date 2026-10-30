@extends('layouts.app')
@section('title', 'Color Picker')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>colorpicker</code> field picks a hex color from a row of swatches.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="default">
        @include('reference._heading', ['id' => 'default', 'title' => 'Default swatches', 'caption' => 'The built-in swatches and a picker for any color.'])
        @include('reference._form_example', ['name' => 'default'])
    </section>

    <section class="ui-section" id="fixed-swatches">
        @include('reference._heading', ['id' => 'fixed-swatches', 'title' => 'Fixed swatches', 'caption' => '<code>availableColors</code> with <code>allowCustom: false</code>.'])
        @include('reference._form_example', ['name' => 'fixed-swatches'])
    </section>

    <section class="ui-section" id="model-colors">
        @include('reference._heading', ['id' => 'model-colors', 'title' => 'Swatches from the model', 'caption' => '<code>availableColors</code> naming <code>Sample::getBrandColors()</code>.'])
        @include('reference._form_example', ['name' => 'model-colors'])
    </section>

    <section class="ui-section" id="alpha">
        @include('reference._heading', ['id' => 'alpha', 'title' => 'Opacity and clearing', 'caption' => '<code>showAlpha</code> and <code>allowEmpty</code>.'])
        @include('reference._form_example', ['name' => 'alpha'])
    </section>

    <section class="ui-section" id="input-only">
        @include('reference._heading', ['id' => 'input-only', 'title' => 'Picker only', 'caption' => '<code>showInput</code> with no swatches.'])
        @include('reference._form_example', ['name' => 'input-only'])
    </section>

    <section class="ui-section" id="read-only">
        @include('reference._heading', ['id' => 'read-only', 'title' => 'Read-only', 'caption' => '<code>readOnly</code> shows the color without the picker.'])
        @include('reference._form_example', ['name' => 'read-only'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
