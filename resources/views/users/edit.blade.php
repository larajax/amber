@extends('layouts.app')
@section('title', 'Edit User')
@section('content')

<form data-request-flash>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="mb-0">Edit User</h1>
        <a href="{{ url('users') }}" class="btn btn-link text-decoration-none">&larr; Back to users</a>
    </div>

    {!! $formWidget->render() !!}

    <div class="mt-4 d-flex gap-2">
        {{ Ui::ajaxButton(handler: 'onUpdate', label: 'Save', primary: true) }}
        {{ Ui::ajaxButton(
            handler: 'onDestroy',
            label: 'Delete',
            danger: true,
            dataRequestConfirm: 'Delete this user?'
        ) }}
    </div>
</form>

@endsection
