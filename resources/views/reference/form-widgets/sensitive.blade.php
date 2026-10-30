@extends('layouts.app')
@section('title', 'Sensitive')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>sensitive</code> field keeps a stored secret off the page until it is revealed.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="default">
        @include('reference._heading', ['id' => 'default', 'title' => 'Masked value', 'caption' => 'A placeholder in place of the secret, revealed over AJAX.'])
        @include('reference._form_example', ['name' => 'default'])
    </section>

    <section class="ui-section" id="allow-copy">
        @include('reference._heading', ['id' => 'allow-copy', 'title' => 'Copy button', 'caption' => '<code>allowCopy</code> adds a copy to clipboard button.'])
        @include('reference._form_example', ['name' => 'allow-copy'])
    </section>

    <section class="ui-section" id="textarea">
        @include('reference._heading', ['id' => 'textarea', 'title' => 'Textarea mode', 'caption' => '<code>mode: textarea</code> for multi-line secrets.'])
        @include('reference._form_example', ['name' => 'textarea'])
    </section>

    <section class="ui-section" id="read-only">
        @include('reference._heading', ['id' => 'read-only', 'title' => 'Read-only', 'caption' => '<code>readOnly</code> can reveal and copy, but not change.'])
        @include('reference._form_example', ['name' => 'read-only'])
    </section>

    <section class="ui-section" id="tab-change">
        @include('reference._heading', ['id' => 'tab-change', 'title' => 'Hiding on tab change', 'caption' => '<code>hideOnTabChange: false</code> keeps a revealed value shown.'])
        @include('reference._form_example', ['name' => 'tab-change'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
