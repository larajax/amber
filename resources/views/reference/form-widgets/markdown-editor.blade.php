@extends('layouts.app')
@section('title', 'Markdown Editor')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>markdown</code> field edits Markdown with a formatting toolbar, a preview and a fullscreen mode. It saves the Markdown text as written.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="default">
        <h2>
            Default editor
        </h2>
        <p class="fg-3">
            The toolbar wraps the selection in Markdown, and the last three buttons switch to the preview, to side by side and to fullscreen. The field <code>placeholder</code> shows while the text is empty.
        </p>
        @include('reference._form_example', ['name' => 'default'])
    </section>

    <section class="ui-section" id="side-by-side">
        <h2>
            Side by side
        </h2>
        <p class="fg-3">
            <code>sideBySide</code> opens the editor with the preview beside the text, the same as <code>mode: split</code>. The toolbar button switches back.
        </p>
        @include('reference._form_example', ['name' => 'side-by-side'])
    </section>

    <section class="ui-section" id="size">
        <h2>
            Size
        </h2>
        <p class="fg-3">
            <code>size</code> sets the starting height: tiny, small, large (the default), huge or giant.
        </p>
        @include('reference._form_example', ['name' => 'size'])
    </section>

    <section class="ui-section" id="safe-mode">
        <h2>
            Safe mode
        </h2>
        <p class="fg-3">
            The preview runs its HTML through DOMPurify, which removes scripts, event handlers and tags such as iframes. <code>safeMode: false</code> shows the HTML as written, so turn it off only for trusted authors.
        </p>
        @include('reference._form_example', ['name' => 'safe-mode'])
    </section>

    <section class="ui-section" id="read-only">
        <h2>
            Read-only
        </h2>
        <p class="fg-3">
            <code>readOnly</code> shows the rendered Markdown instead of the editor, and a hidden input keeps posting the value.
        </p>
        @include('reference._form_example', ['name' => 'read-only'])
    </section>

    <section class="ui-section" id="disabled">
        <h2>
            Disabled
        </h2>
        <p class="fg-3">
            <code>disabled</code> also shows the rendered Markdown, but posts nothing, so Save leaves the value alone.
        </p>
        @include('reference._form_example', ['name' => 'disabled'])
    </section>

    <section class="ui-section" id="preview">
        <h2>
            Preview
        </h2>
        <p class="fg-3">
            In preview mode the field shows the rendered Markdown, with any HTML in it escaped. This example's form is made with <code>previewMode</code> on.
        </p>
        @include('reference._form_example', ['name' => 'preview'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
