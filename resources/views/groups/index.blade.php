@extends('layouts.app')
@section('title', 'Groups')
@section('content')

<h1 class="mb-3">
    Groups
</h1>
<p class="fg-3 mb-7">
    The groups authors belong to. Click a group to edit it and its members in a popup.
</p>

{{ $toolbar }}

{{ $list }}

@endsection
