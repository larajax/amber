@extends('layouts.app')
@section('title', 'Color Picker')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>colorpicker</code> field picks a hex color from a row of swatches, with optional custom colors, an opacity slider and a clear button. It saves the color as a hex string.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="default">
        <h2>
            Default swatches
        </h2>
        <p class="fg-3">
            With no options, the field shows the built-in swatches and a picker for any other color.
        </p>
        @include('reference._form_example', ['name' => 'default'])
    </section>

    <section class="ui-section" id="fixed-swatches">
        <h2>
            Fixed swatches
        </h2>
        <p class="fg-3">
            <code>availableColors</code> replaces the swatches, and <code>allowCustom: false</code> limits the choice to them.
        </p>
        @include('reference._form_example', ['name' => 'fixed-swatches'])
    </section>

    <section class="ui-section" id="model-colors">
        <h2>
            Swatches from the model
        </h2>
        <p class="fg-3">
            <code>availableColors</code> also takes the name of a model method that returns the colors, here <code>Sample::getBrandColors()</code>.
        </p>
        @include('reference._form_example', ['name' => 'model-colors'])
    </section>

    <section class="ui-section" id="alpha">
        <h2>
            Opacity and clearing
        </h2>
        <p class="fg-3">
            <code>showAlpha</code> adds an opacity slider, which saves eight digit hex. <code>allowEmpty</code> adds a clear button that saves no color.
        </p>
        @include('reference._form_example', ['name' => 'alpha'])
    </section>

    <section class="ui-section" id="input-only">
        <h2>
            Picker only
        </h2>
        <p class="fg-3">
            <code>showInput</code> drops the swatches and opens the picker straight away, for any color.
        </p>
        @include('reference._form_example', ['name' => 'input-only'])
    </section>

    <section class="ui-section" id="read-only">
        <h2>
            Read-only
        </h2>
        <p class="fg-3">
            <code>readOnly</code> shows the color and its swatch without the picker, and posts the value unchanged. A <code>disabled</code> field or a form in preview mode looks the same, and posts nothing.
        </p>
        @include('reference._form_example', ['name' => 'read-only'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
