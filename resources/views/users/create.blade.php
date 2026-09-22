@extends('layouts.app')
@section('title', 'New User')
@section('content')

<form data-request-flash>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="mb-0">New User</h1>
        <a href="{{ url('users') }}" class="btn btn-link text-decoration-none">&larr; Back to users</a>
    </div>

    {{ $formWidget }}

    <div class="mt-4">
        {{ Ui::ajaxButton(handler: 'onStore', label: 'Create User', primary: true) }}
    </div>
</form>

@endsection
