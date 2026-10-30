@extends('layouts.app')
@section('title', 'Rich Editor')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>richeditor</code> field edits formatted text with a toolbar, menus that appear on the selection and an HTML source view. It saves the content as HTML.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="default">
        <h2>
            Default toolbar
        </h2>
        <p class="fg-3">
            With no options, the toolbar has paragraph formats, alignment, bold, italic, underline, lists, tables, links, images, a horizontal line and the HTML view. Images go in by address until uploads are set up, and the snippet button waits for a page that provides snippets.
        </p>
        @include('reference._form_example', ['name' => 'default'])
    </section>

    <section class="ui-section" id="toolbar">
        <h2>
            Toolbar buttons
        </h2>
        <p class="fg-3">
            <code>toolbarButtons</code> lists the buttons in order, with <code>'|'</code> for a separator. It also takes a pipe separated string, such as <code>bold|italic||insertLink</code>, where a double pipe is a separator.
        </p>
        @include('reference._form_example', ['name' => 'toolbar'])
    </section>

    <section class="ui-section" id="full-toolbar">
        <h2>
            Every button
        </h2>
        <p class="fg-3">
            The built-in buttons that work on any page. <code>insertFile</code> needs uploads, as in the next example, while <code>insertPageLink</code>, <code>insertSnippet</code> and the media pickers reach further only when the page script sets the matching hooks.
        </p>
        @include('reference._form_example', ['name' => 'full-toolbar'])
    </section>

    <section class="ui-section" id="uploads">
        <h2>
            Uploads
        </h2>
        <p class="fg-3">
            <code>uploadDisk</code> turns on the field's <code>onUploadFile</code> handler, which stores files under <code>uploadPath</code> on that disk and returns their address. Images accept jpg, png, gif and webp, file links also accept documents such as pdf, docx and zip, and <code>uploadMaxSize</code> limits each file in megabytes.
        </p>
        @include('reference._form_example', ['name' => 'uploads'])
    </section>

    <section class="ui-section" id="line-breaks">
        <h2>
            Line breaks
        </h2>
        <p class="fg-3">
            <code>useLineBreaks</code> makes Enter add a <code>&lt;br&gt;</code> rather than start a new paragraph, for addresses and signatures.
        </p>
        @include('reference._form_example', ['name' => 'line-breaks'])
    </section>

    <section class="ui-section" id="editor-options">
        <h2>
            Editor options
        </h2>
        <p class="fg-3">
            <code>editorOptions</code> passes settings to the editor: the <code>paragraphFormat</code> list, the class names offered by <code>paragraphStyles</code>, <code>inlineStyles</code>, <code>linkStyles</code>, <code>imageStyles</code> and <code>tableStyles</code>, and image placement with <code>imageDefaultAlign</code> and <code>imageResize</code>.
        </p>
        @include('reference._form_example', ['name' => 'editor-options'])
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

    <section class="ui-section" id="read-only">
        <h2>
            Read-only
        </h2>
        <p class="fg-3">
            <code>readOnly</code> keeps the editor with its toolbar disabled. A <code>disabled</code> field looks the same, and its value is not saved.
        </p>
        @include('reference._form_example', ['name' => 'read-only'])
    </section>

    <section class="ui-section" id="preview">
        <h2>
            Preview
        </h2>
        <p class="fg-3">
            In preview mode the field shows the HTML itself, cleaned of scripts and event handlers. <code>safeMode: false</code> shows it unchanged. This example's form is made with <code>previewMode</code> on.
        </p>
        @include('reference._form_example', ['name' => 'preview'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
