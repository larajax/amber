@extends('layouts.app')
@section('title', 'Code Editor')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>codeeditor</code> field edits code in the Monaco editor and saves it as plain text.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="default">
        @include('reference._heading', ['id' => 'default', 'title' => 'Default editor', 'caption' => 'PHP highlighting with the toolbar and its shortcuts.'])
        @include('reference._form_example', ['name' => 'default'])
    </section>

    <section class="ui-section" id="language">
        @include('reference._heading', ['id' => 'language', 'title' => 'Language', 'caption' => '<code>language: twig</code>, where Emmet abbreviations expand.'])
        @include('reference._form_example', ['name' => 'language'])
    </section>

    <section class="ui-section" id="size">
        @include('reference._heading', ['id' => 'size', 'title' => 'Size', 'caption' => '<code>size: small</code> for a shorter starting height.'])
        @include('reference._form_example', ['name' => 'size'])
    </section>

    <section class="ui-section" id="word-wrap">
        @include('reference._heading', ['id' => 'word-wrap', 'title' => 'Word wrap', 'caption' => '<code>wordWrap: 80</code> with <code>showPrintMargin</code>.'])
        @include('reference._form_example', ['name' => 'word-wrap'])
    </section>

    <section class="ui-section" id="indentation">
        @include('reference._heading', ['id' => 'indentation', 'title' => 'Indentation and folding', 'caption' => '<code>tabSize</code>, <code>showInvisibles</code> and <code>codeFolding</code>.'])
        @include('reference._form_example', ['name' => 'indentation'])
    </section>

    <section class="ui-section" id="appearance">
        @include('reference._heading', ['id' => 'appearance', 'title' => 'Appearance', 'caption' => '<code>fontSize</code>, <code>margin</code>, and the gutter, highlight and guides off.'])
        @include('reference._form_example', ['name' => 'appearance'])
    </section>

    <section class="ui-section" id="typing">
        @include('reference._heading', ['id' => 'typing', 'title' => 'Typing aids', 'caption' => '<code>autocompletion: live</code> and <code>autoClosing: false</code>.'])
        @include('reference._form_example', ['name' => 'typing'])
    </section>

    <section class="ui-section" id="read-only">
        @include('reference._heading', ['id' => 'read-only', 'title' => 'Read-only', 'caption' => '<code>readOnly</code> keeps the editor with its editing buttons disabled.'])
        @include('reference._form_example', ['name' => 'read-only'])
    </section>

    <section class="ui-section" id="preview">
        @include('reference._heading', ['id' => 'preview', 'title' => 'Preview', 'caption' => 'Plain text code in a <code>previewMode</code> form.'])
        @include('reference._form_example', ['name' => 'preview'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
