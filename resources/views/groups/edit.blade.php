@extends('layouts.app')
@section('title', 'Edit Group')
@section('content')

<form data-request="onUpdate" data-request-flash>
    <div class="d-flex align-items-center justify-content-between mb-7">
        <h1 class="mb-0">
            Edit Group
        </h1>
        <a href="{{ route('groups.index') }}" class="btn-link text-decoration-none">
            &larr; Back to groups
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
            dataRequestConfirm: 'Delete this group? Its members keep their accounts.'
        ) }}
    </div>
</form>

@endsection
