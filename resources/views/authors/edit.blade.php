@extends('layouts.app')
@section('title', 'Edit Author')
@section('content')

<form data-request="onUpdate" data-request-flash>
    <div class="d-flex align-items-center justify-content-between mb-7">
        <h1 class="mb-0">
            Edit Author
        </h1>
        <a href="{{ route('authors.index') }}" class="btn-link text-decoration-none">
            &larr; Back to authors
        </a>
    </div>

    {{ $formWidget }}

    <div class="mt-7 d-flex gap-3">
        {{ Ui::ajaxButton(dataRequestFlash: true, handler: 'onUpdate', label: 'Save', primary: true) }}
        {{ Ui::ajaxButton(
            dataRequestFlash: true,
            handler: 'onDestroy',
            label: 'Delete',
            danger: true,
            dataRequestConfirm: 'Delete this author? Their posts stay, without an author.'
        ) }}
    </div>
</form>

@endsection
