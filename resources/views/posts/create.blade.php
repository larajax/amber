@extends('layouts.app')
@section('title', 'New Post')
@section('content')

<form data-request="onStore" data-request-flash>
    <div class="d-flex align-items-center justify-content-between mb-7">
        <h1 class="mb-0">
            New Post
        </h1>
        <a href="{{ url('posts') }}" class="btn-link text-decoration-none">
            &larr; Back to posts
        </a>
    </div>

    {{ $formWidget }}

    <div class="mt-7">
        {{ Ui::ajaxButton(
            dataRequestFlash: true,
            handler: 'onStore',
            label: 'Create Post',
            primary: true,
            hotkey: ['ctrl+s', 'cmd+s']
        ) }}
    </div>
</form>

@endsection
