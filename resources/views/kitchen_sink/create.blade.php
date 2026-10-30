@extends('layouts.app')
@section('title', 'New Sample')
@section('content')

<form data-request="onStore" data-request-flash>
    {{-- Deferred uploads and the save share this key, so files added before the first save are attached --}}
    <input type="hidden" name="_session_key" value="{{ $formWidget->getSessionKey() }}" />

    <div class="d-flex align-items-center justify-content-between mb-7">
        <h1 class="mb-0">
            New Sample
        </h1>
        <a href="{{ url('kitchen-sink') }}" class="btn-link text-decoration-none">
            &larr; Back to the kitchen sink
        </a>
    </div>

    {{ $formWidget }}

    <div class="mt-7">
        {{ Ui::ajaxButton(dataRequestFlash: true, handler: 'onStore', label: 'Create Sample', primary: true) }}
    </div>
</form>

@endsection
