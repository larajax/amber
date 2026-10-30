@extends('layouts.app')
@section('title', 'Code Editor')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>codeeditor</code> field edits code in the Monaco editor, with syntax highlighting, a toolbar and a fullscreen mode. It saves the code as plain text.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="default">
        <h2>
            Default editor
        </h2>
        <p class="fg-3">
            With no options, the editor highlights PHP, wraps long lines at the field edge and follows the page color mode. The toolbar buttons have shortcuts too, such as Ctrl+F to find and Ctrl+Shift+F for fullscreen, which Escape leaves.
        </p>
        @include('reference._form_example', ['name' => 'default'])
    </section>

    <section class="ui-section" id="language">
        <h2>
            Language
        </h2>
        <p class="fg-3">
            <code>language</code> picks the highlighting: php, html, css, scss, less, javascript, json, yaml, markdown, twig or plaintext. Emmet expands in html, twig and php, and Format Code works for html, css, scss, less and json.
        </p>
        @include('reference._form_example', ['name' => 'language'])
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

    <section class="ui-section" id="word-wrap">
        <h2>
            Word wrap
        </h2>
        <p class="fg-3">
            <code>wordWrap</code> is fluid (the default), off, 40 or 80, and <code>showPrintMargin</code> draws a ruler at that column. The Word Wrap button turns wrapping on and off while editing.
        </p>
        @include('reference._form_example', ['name' => 'word-wrap'])
    </section>

    <section class="ui-section" id="indentation">
        <h2>
            Indentation and folding
        </h2>
        <p class="fg-3">
            <code>tabSize</code> sets the indent width, and <code>useSoftTabs: false</code> indents with tabs instead of spaces. <code>showInvisibles</code> shows spaces and tabs, and <code>codeFolding: markbegin</code> keeps the fold arrows in view rather than showing them on hover.
        </p>
        @include('reference._form_example', ['name' => 'indentation'])
    </section>

    <section class="ui-section" id="appearance">
        <h2>
            Appearance
        </h2>
        <p class="fg-3">
            <code>fontSize</code> and <code>margin</code> set the text size and the space above and below it. <code>showGutter</code>, <code>highlightActiveLine</code> and <code>displayIndentGuides</code> turn off the line numbers, the current line highlight and the indent guides. <code>theme</code> takes vs, vs-dark, hc-black or hc-light, but Monaco applies one theme to the whole page, so these editors leave it empty to follow the page color mode.
        </p>
        @include('reference._form_example', ['name' => 'appearance'])
    </section>

    <section class="ui-section" id="typing">
        <h2>
            Typing aids
        </h2>
        <p class="fg-3">
            <code>autocompletion: live</code> suggests words and properties while typing, where manual (the default) waits for Ctrl+Space. <code>autoClosing: false</code> stops the editor closing brackets, quotes and tags, and <code>enableSnippets: false</code> leaves snippets out of the suggestions.
        </p>
        @include('reference._form_example', ['name' => 'typing'])
    </section>

    <section class="ui-section" id="read-only">
        <h2>
            Read-only
        </h2>
        <p class="fg-3">
            <code>readOnly</code> keeps the editor and its toolbar, with the editing buttons disabled. A <code>disabled</code> field looks the same, and its value is not saved.
        </p>
        @include('reference._form_example', ['name' => 'read-only'])
    </section>

    <section class="ui-section" id="preview">
        <h2>
            Preview
        </h2>
        <p class="fg-3">
            In preview mode the field shows the code as plain text, without the editor. This example's form is made with <code>previewMode</code> on.
        </p>
        @include('reference._form_example', ['name' => 'preview'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
