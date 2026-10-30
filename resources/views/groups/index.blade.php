@extends('layouts.app')
@section('title', 'Groups')
@section('content')

<h1 class="mb-3">
    Groups
</h1>
<p class="fg-3 mb-7">
    The groups authors belong to. Click a group to edit it and its members in a popup.
    <a href="{{ config('reference.docs_url') }}/guides/lists#popup-editing" target="_blank" rel="noopener">
        How this page is built
    </a>
</p>

{{ $toolbar }}

{{ $list }}

@endsection
