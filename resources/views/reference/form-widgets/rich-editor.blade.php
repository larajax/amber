@extends('layouts.app')
@section('title', 'Rich Editor')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>richeditor</code> field edits formatted text with a toolbar and saves it as HTML.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="default">
        @include('reference._heading', ['id' => 'default', 'title' => 'Default toolbar', 'caption' => 'The default toolbar and the selection menus.'])
        @include('reference._form_example', ['name' => 'default'])
    </section>

    <section class="ui-section" id="toolbar">
        @include('reference._heading', ['id' => 'toolbar', 'title' => 'Toolbar buttons', 'caption' => '<code>toolbarButtons</code> with separators.'])
        @include('reference._form_example', ['name' => 'toolbar'])
    </section>

    <section class="ui-section" id="full-toolbar">
        @include('reference._heading', ['id' => 'full-toolbar', 'title' => 'Every button', 'caption' => 'Every built-in button that works without a page hook.'])
        @include('reference._form_example', ['name' => 'full-toolbar'])
    </section>

    <section class="ui-section" id="uploads">
        @include('reference._heading', ['id' => 'uploads', 'title' => 'Uploads', 'caption' => '<code>uploadDisk</code>, <code>uploadPath</code> and <code>uploadMaxSize</code>.'])
        @include('reference._form_example', ['name' => 'uploads'])
    </section>

    <section class="ui-section" id="line-breaks">
        @include('reference._heading', ['id' => 'line-breaks', 'title' => 'Line breaks', 'caption' => '<code>useLineBreaks</code> makes Enter add a line break.'])
        @include('reference._form_example', ['name' => 'line-breaks'])
    </section>

    <section class="ui-section" id="editor-options">
        @include('reference._heading', ['id' => 'editor-options', 'title' => 'Editor options', 'caption' => '<code>editorOptions</code> for formats, styles and image placement.'])
        @include('reference._form_example', ['name' => 'editor-options'])
    </section>

    <section class="ui-section" id="size">
        @include('reference._heading', ['id' => 'size', 'title' => 'Size', 'caption' => '<code>size: small</code> for a shorter starting height.'])
        @include('reference._form_example', ['name' => 'size'])
    </section>

    <section class="ui-section" id="read-only">
        @include('reference._heading', ['id' => 'read-only', 'title' => 'Read-only', 'caption' => '<code>readOnly</code> keeps the editor with its toolbar disabled.'])
        @include('reference._form_example', ['name' => 'read-only'])
    </section>

    <section class="ui-section" id="preview">
        @include('reference._heading', ['id' => 'preview', 'title' => 'Preview', 'caption' => 'Cleaned HTML in a <code>previewMode</code> form.'])
        @include('reference._form_example', ['name' => 'preview'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
