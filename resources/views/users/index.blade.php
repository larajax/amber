@extends('layouts.app')
@section('title', 'Users')
@section('content')

<h1 class="mb-4">Users</h1>

{!! $toolbar->render() !!}

{!! $filter->render() !!}

{!! $list->render() !!}

@endsection
