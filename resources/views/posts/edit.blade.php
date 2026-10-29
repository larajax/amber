@extends('layouts.app')
@section('title', 'Edit Post')
@section('content')

<form data-request="onUpdate" data-request-flash>
    <div class="d-flex align-items-center justify-content-between mb-7">
        <h1 class="mb-0">
            Edit Post
        </h1>
        <a href="{{ url('posts') }}" class="btn-link text-decoration-none">
            &larr; Back to posts
        </a>
    </div>

    {{ $formWidget }}

    <div class="mt-7 d-flex gap-3">
        {{ Ui::ajaxButton(
            handler: 'onUpdate',
            label: 'Save',
            primary: true,
            hotkey: ['ctrl+s', 'cmd+s']
        ) }}
        {{ Ui::ajaxButton(
            handler: 'onDestroy',
            label: 'Delete',
            danger: true,
            dataRequestConfirm: 'Delete this post with its comments?'
        ) }}
    </div>
</form>

@endsection
