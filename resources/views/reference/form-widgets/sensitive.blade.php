@extends('layouts.app')
@section('title', 'Sensitive')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>sensitive</code> field keeps a stored secret off the page. It renders a placeholder, fetches the real value over AJAX only when revealed, and saves the stored value again when the placeholder comes back unchanged.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="default">
        <h2>
            Masked value
        </h2>
        <p class="fg-3">
            The input holds <code>__hidden__</code> rather than the secret. The eye button calls the field's <code>onShowValue</code> handler and shows the answer, and typing clears the placeholder for a new value. Change it with <code>hiddenPlaceholder</code>.
        </p>
        @include('reference._form_example', ['name' => 'default'])
    </section>

    <section class="ui-section" id="allow-copy">
        <h2>
            Copy button
        </h2>
        <p class="fg-3">
            <code>allowCopy</code> adds a button that copies the value to the clipboard, revealing it first when needed and hiding it again after.
        </p>
        @include('reference._form_example', ['name' => 'allow-copy'])
    </section>

    <section class="ui-section" id="textarea">
        <h2>
            Textarea mode
        </h2>
        <p class="fg-3">
            <code>mode: textarea</code> shows a row of dots in place of a multi-line value, with Reveal Contents and Copy to Clipboard below it.
        </p>
        @include('reference._form_example', ['name' => 'textarea'])
    </section>

    <section class="ui-section" id="read-only">
        <h2>
            Read-only
        </h2>
        <p class="fg-3">
            <code>readOnly</code> disables the input but keeps the reveal and copy buttons, and the value is not posted. A <code>disabled</code> field behaves the same way.
        </p>
        @include('reference._form_example', ['name' => 'read-only'])
    </section>

    <section class="ui-section" id="tab-change">
        <h2>
            Hiding on tab change
        </h2>
        <p class="fg-3">
            A revealed value hides again when the browser tab is hidden, so it is not left on screen. <code>hideOnTabChange: false</code> leaves it revealed.
        </p>
        @include('reference._form_example', ['name' => 'tab-change'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
