@extends('layouts.app')
@section('title', 'New Author')
@section('content')

<form data-request="onStore" data-request-flash>
    <div class="d-flex align-items-center justify-content-between mb-7">
        <h1 class="mb-0">
            New Author
        </h1>
        <a href="{{ route('authors.index') }}" class="btn-link text-decoration-none">
            &larr; Back to authors
        </a>
    </div>

    {{ $formWidget }}

    <div class="mt-7">
        {{ Ui::ajaxButton(dataRequestFlash: true, handler: 'onStore', label: 'Create Author', primary: true) }}
    </div>
</form>

@endsection
