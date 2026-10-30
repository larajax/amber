@extends('layouts.app')
@section('title', 'Reference')
@section('content')

@include('reference._header')

{{ Ui::callout()->label('This page is being built')->comment('Its examples are on the way.') }}

@endsection
