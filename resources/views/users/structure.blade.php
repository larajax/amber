@extends('layouts.app')
@section('title', 'Users — Structure')
@section('content')

<h1 class="mb-2">Users</h1>
<p class="text-muted mb-4">Drag rows to reorder. The new order is persisted through the model's <code>setSortableOrder()</code> method.</p>

{!! $widget->render() !!}

@endsection
