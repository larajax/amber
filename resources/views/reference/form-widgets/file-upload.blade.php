@extends('layouts.app')
@section('title', 'File Upload')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>fileupload</code> field uploads files to an attachOne or attachMany relation, by clicking Upload or dropping files on the field. New uploads and removals wait against the page\'s session key, and Save commits them.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="image-single">
        <h2>
            Single image
        </h2>
        <p class="fg-3">
            <code>mode: image</code> on an attachOne relation holds one image, and a new upload replaces it. <code>imageWidth</code> and <code>imageHeight</code> set the thumbnail size.
        </p>
        @include('reference._form_example', ['name' => 'image-single'])
    </section>

    <section class="ui-section" id="image-multi">
        <h2>
            Multiple images
        </h2>
        <p class="fg-3">
            On an attachMany relation the field lists every image. Drag the handle to sort them, or check several and delete them together. <code>maxFiles</code> caps the count, <code>fileTypes</code> limits the extensions, and <code>thumbOptions</code> crops the thumbnails.
        </p>
        @include('reference._form_example', ['name' => 'image-multi'])
    </section>

    <section class="ui-section" id="file-single">
        <h2>
            Single file
        </h2>
        <p class="fg-3">
            <code>mode: file</code> lists the file by name and size. <code>mimeTypes</code> also checks the content type, and <code>maxFilesize</code> sets the limit in megabytes.
        </p>
        @include('reference._form_example', ['name' => 'file-single'])
    </section>

    <section class="ui-section" id="file-multi">
        <h2>
            Multiple files
        </h2>
        <p class="fg-3">
            An attachMany relation in file mode lists the files with a checkbox and drag handle each. <code>size</code> sets how tall the list grows before it scrolls.
        </p>
        @include('reference._form_example', ['name' => 'file-multi'])
    </section>

    <section class="ui-section" id="private">
        <h2>
            Private files
        </h2>
        <p class="fg-3">
            The field follows the relation: <code>'public' =&gt; false</code> on the model stores the file in the protected uploads folder.
        </p>
        @include('reference._form_example', ['name' => 'private'])
    </section>

    <section class="ui-section" id="attach-on-upload">
        <h2>
            Attach on upload
        </h2>
        <p class="fg-3">
            <code>deferredBinding: false</code> attaches each upload to a saved record straight away, instead of waiting for Save.
        </p>
        @include('reference._form_example', ['name' => 'attach-on-upload'])
    </section>

    <section class="ui-section" id="preview">
        <h2>
            Preview
        </h2>
        <p class="fg-3">
            <code>disabled</code> puts the field in preview mode, without the toolbar, and with no files it shows a message instead. This copy reads the gallery through <code>valueFrom</code>.
        </p>
        @include('reference._form_example', ['name' => 'preview'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
