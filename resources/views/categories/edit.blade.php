@extends('layouts.app')
@section('title', 'Edit Category')
@section('content')

<form data-request="onUpdate" data-request-flash>
    <div class="d-flex align-items-center justify-content-between mb-7">
        <h1 class="mb-0">
            Edit Category
        </h1>
        <a href="{{ url('categories') }}" class="btn-link text-decoration-none">
            &larr; Back to categories
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
            dataRequestConfirm: 'Delete this category and the categories below it?'
        ) }}
    </div>
</form>

@endsection
