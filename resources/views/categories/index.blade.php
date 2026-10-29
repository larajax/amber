@extends('layouts.app')
@section('title', 'Categories')
@section('content')

<h1 class="mb-3">
    Categories
</h1>
<p class="fg-3 mb-7">
    A nested set tree up to three levels deep. Drag a row by its handle to reorder it or nest it under another category, and use the arrows to expand or collapse a branch.
</p>

{{ $toolbar }}

{{ $list }}

@endsection
