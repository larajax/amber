@extends('layouts.app')
@section('title', 'File Upload')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>fileupload</code> field uploads images and files to an attachOne or attachMany relation.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="image-single">
        @include('reference._heading', ['id' => 'image-single', 'title' => 'Single image', 'caption' => '<code>mode: image</code> on an attachOne relation, with <code>imageWidth</code> and <code>imageHeight</code>.'])
        @include('reference._form_example', ['name' => 'image-single'])
    </section>

    <section class="ui-section" id="image-multi">
        @include('reference._heading', ['id' => 'image-multi', 'title' => 'Multiple images', 'caption' => 'An attachMany gallery with <code>maxFiles</code>, <code>fileTypes</code> and <code>thumbOptions</code>.'])
        @include('reference._form_example', ['name' => 'image-multi'])
    </section>

    <section class="ui-section" id="file-single">
        @include('reference._heading', ['id' => 'file-single', 'title' => 'Single file', 'caption' => '<code>mode: file</code> with <code>fileTypes</code>, <code>mimeTypes</code> and <code>maxFilesize</code>.'])
        @include('reference._form_example', ['name' => 'file-single'])
    </section>

    <section class="ui-section" id="file-multi">
        @include('reference._heading', ['id' => 'file-multi', 'title' => 'Multiple files', 'caption' => 'An attachMany file list with <code>maxFiles</code> and <code>size</code>.'])
        @include('reference._form_example', ['name' => 'file-multi'])
    </section>

    <section class="ui-section" id="private">
        @include('reference._heading', ['id' => 'private', 'title' => 'Private files', 'caption' => "A relation set to <code>'public' =&gt; false</code> on the model."])
        @include('reference._form_example', ['name' => 'private'])
    </section>

    <section class="ui-section" id="attach-on-upload">
        @include('reference._heading', ['id' => 'attach-on-upload', 'title' => 'Attach on upload', 'caption' => '<code>deferredBinding: false</code> attaches each upload straight away.'])
        @include('reference._form_example', ['name' => 'attach-on-upload'])
    </section>

    <section class="ui-section" id="preview">
        @include('reference._heading', ['id' => 'preview', 'title' => 'Preview', 'caption' => '<code>disabled</code> shows the gallery through <code>valueFrom</code>, without the toolbar.'])
        @include('reference._form_example', ['name' => 'preview'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
