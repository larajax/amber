@extends('layouts.app')
@section('title', 'New Category')
@section('content')

<form data-request="onStore" data-request-flash>
    <div class="d-flex align-items-center justify-content-between mb-7">
        <h1 class="mb-0">
            New Category
        </h1>
        <a href="{{ route('categories.index') }}" class="btn-link text-decoration-none">
            &larr; Back to categories
        </a>
    </div>

    {{ $formWidget }}

    <div class="mt-7">
        {{ Ui::ajaxButton(dataRequestFlash: true, handler: 'onStore', label: 'Create Category', primary: true) }}
    </div>
</form>

@endsection
