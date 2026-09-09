@extends('layouts.app')
@section('title', 'Edit Group')
@section('content')

<form data-request-flash>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="mb-0">Edit Group</h1>
        <a href="{{ url('user-groups') }}" class="btn btn-link text-decoration-none">&larr; Back to groups</a>
    </div>

    {!! $formWidget->render() !!}

    <div class="mt-4 d-flex gap-2">
        {{ Ui::ajaxButton(handler: 'onUpdate', label: 'Save', primary: true) }}
        {{ Ui::ajaxButton(
            handler: 'onDestroy',
            label: 'Delete',
            danger: true,
            dataRequestConfirm: 'Delete this group?'
        ) }}
    </div>
</form>

@endsection
