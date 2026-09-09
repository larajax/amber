@extends('layouts.app')
@section('title', 'New Group')
@section('content')

<form data-request-flash>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="mb-0">New Group</h1>
        <a href="{{ url('user-groups') }}" class="btn btn-link text-decoration-none">&larr; Back to groups</a>
    </div>

    {!! $formWidget->render() !!}

    <div class="mt-4">
        {{ Ui::ajaxButton(handler: 'onStore', label: 'Create Group', primary: true) }}
    </div>
</form>

@endsection
