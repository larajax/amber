@extends('layouts.app')
@section('title', 'Phone')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>phone</code> field takes a phone number with a country picker, and saves it in the international E.164 format.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="default">
        @include('reference._heading', ['id' => 'default', 'title' => 'Phone number', 'caption' => 'Formatted as it is typed, with <code>defaultCountry</code> for numbers typed without a country code.'])
        @include('reference._form_example', ['name' => 'default'])
    </section>

    <section class="ui-section" id="countries">
        @include('reference._heading', ['id' => 'countries', 'title' => 'Choosing the countries', 'caption' => '<code>countries</code> and <code>preferredCountries</code>.'])
        @include('reference._form_example', ['name' => 'countries'])
    </section>

    <section class="ui-section" id="read-only">
        @include('reference._heading', ['id' => 'read-only', 'title' => 'Read-only', 'caption' => '<code>readOnly</code> shows the number with the picker locked.'])
        @include('reference._form_example', ['name' => 'read-only'])
    </section>

    <section class="ui-section" id="preview">
        @include('reference._heading', ['id' => 'preview', 'title' => 'Preview mode', 'caption' => 'A link that calls the number, in the format of its country.'])
        @include('reference._form_example', ['name' => 'preview'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
