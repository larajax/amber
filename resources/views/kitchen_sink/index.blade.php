@extends('layouts.app')
@section('title', 'Kitchen Sink')
@section('content')

<h1 class="mb-3">
    Kitchen Sink
</h1>
<p class="fg-3 mb-7">
    Each sample opens a form that shows every field type, form widget and form option, organised into tabs.
</p>

{{ $toolbar }}

{{ $list }}

@endsection
