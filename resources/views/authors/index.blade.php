@extends('layouts.app')
@section('title', 'Authors')
@section('content')

<h1 class="mb-3">
    Authors
</h1>
<p class="fg-3 mb-7">
    The people who write the posts. Click an author to edit their account and groups, or check rows to delete them; their posts stay, without an author.
    <a href="{{ config('reference.docs_url') }}/guides/models#what-works-on-eloquent" target="_blank" rel="noopener">
        How this page is built
    </a>
</p>

{{ $toolbar }}

{{ $filter }}

{{ $list }}

@endsection
