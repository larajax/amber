@extends('layouts.app')
@section('title', 'Edit Post')
@section('content')

<form data-request="onUpdate" data-request-flash>
    {{-- The save posts the same key the featured image upload was held against, which attaches it --}}
    <input type="hidden" name="_session_key" value="{{ $formWidget->getSessionKey() }}" />

    <div class="d-flex align-items-center justify-content-between mb-7">
        <h1 class="mb-0">
            Edit Post
        </h1>
        <a href="{{ route('posts.index') }}" class="btn-link text-decoration-none">
            &larr; Back to posts
        </a>
    </div>

    {{ $formWidget }}

    <div class="mt-7 d-flex gap-3">
        {{ Ui::ajaxButton(
            dataRequestFlash: true,
            handler: 'onUpdate',
            label: 'Save',
            primary: true,
            hotkey: ['ctrl+s', 'cmd+s']
        ) }}
        {{ Ui::ajaxButton(
            dataRequestFlash: true,
            handler: 'onDestroy',
            label: 'Delete',
            danger: true,
            dataRequestConfirm: 'Delete this post with its comments?'
        ) }}
    </div>
</form>

@endsection
