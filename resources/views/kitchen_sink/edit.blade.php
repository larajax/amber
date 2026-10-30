@extends('layouts.app')
@section('title', 'Edit Sample')
@section('content')

<form data-request="onUpdate" data-request-flash>
    {{-- Deferred uploads and removals are committed by the save that posts this same key --}}
    <input type="hidden" name="_session_key" value="{{ $formWidget->getSessionKey() }}" />

    <div class="d-flex align-items-center justify-content-between mb-7">
        <h1 class="mb-0">
            Edit Sample
        </h1>
        <a href="{{ url('kitchen-sink') }}" class="btn-link text-decoration-none">
            &larr; Back to the kitchen sink
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
            dataRequestConfirm: 'Delete this sample?'
        ) }}
    </div>
</form>

@endsection
