@extends('layouts.app')
@section('title', 'Posts')
@section('content')

<h1 class="mb-3">
    Posts
</h1>
<p class="fg-3 mb-7">
    Archived posts are filtered out by default. Check rows to run a bulk action, press Shift+Q for a quick post, and use the list setup icon beside the search to choose columns and page size.
</p>

{{ $toolbar }}

{{ $filter }}

{{ $list }}

@endsection
