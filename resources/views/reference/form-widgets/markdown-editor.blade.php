@extends('layouts.app')
@section('title', 'Markdown Editor')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>markdown</code> field edits Markdown with a toolbar and a preview, and saves it as written.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="default">
        @include('reference._heading', ['id' => 'default', 'title' => 'Default editor', 'caption' => 'The formatting toolbar and a <code>placeholder</code>.'])
        @include('reference._form_example', ['name' => 'default'])
    </section>

    <section class="ui-section" id="side-by-side">
        @include('reference._heading', ['id' => 'side-by-side', 'title' => 'Side by side', 'caption' => '<code>sideBySide</code> opens the preview beside the text.'])
        @include('reference._form_example', ['name' => 'side-by-side'])
    </section>

    <section class="ui-section" id="size">
        @include('reference._heading', ['id' => 'size', 'title' => 'Size', 'caption' => '<code>size: small</code> for a shorter starting height.'])
        @include('reference._form_example', ['name' => 'size'])
    </section>

    <section class="ui-section" id="safe-mode">
        @include('reference._heading', ['id' => 'safe-mode', 'title' => 'Safe mode', 'caption' => '<code>safeMode: false</code> shows HTML in the preview as written.'])
        @include('reference._form_example', ['name' => 'safe-mode'])
    </section>

    <section class="ui-section" id="read-only">
        @include('reference._heading', ['id' => 'read-only', 'title' => 'Read-only', 'caption' => '<code>readOnly</code> shows rendered Markdown and still saves it.'])
        @include('reference._form_example', ['name' => 'read-only'])
    </section>

    <section class="ui-section" id="disabled">
        @include('reference._heading', ['id' => 'disabled', 'title' => 'Disabled', 'caption' => '<code>disabled</code> shows rendered Markdown and saves nothing.'])
        @include('reference._form_example', ['name' => 'disabled'])
    </section>

    <section class="ui-section" id="preview">
        @include('reference._heading', ['id' => 'preview', 'title' => 'Preview', 'caption' => 'Rendered Markdown in a <code>previewMode</code> form.'])
        @include('reference._form_example', ['name' => 'preview'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
