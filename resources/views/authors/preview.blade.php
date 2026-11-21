@extends('layouts.app')
@section('title', $user->name)
@section('content')

<div class="d-flex align-items-center justify-content-between mb-7">
    <h1 class="mb-0">
        {{ $user->name }}
    </h1>
    <a href="{{ route('authors.index') }}" class="btn-link text-decoration-none">
        &larr; Back to authors
    </a>
</div>

@include('authors._scoreboard')

<div class="mb-7">
    <div class="control-toolbar form-toolbar" data-control="toolbar">
        {{ Ui::button(
            label: 'Edit',
            href: route('authors.edit', $user),
            icon: 'ph ph-pencil-simple',
            primary: true,
            outline: true
        ) }}

        {{ Ui::ajaxButton(
            label: 'Delete',
            handler: 'onDestroy',
            icon: 'ph ph-trash',
            danger: true,
            outline: true,
            dataRequestFlash: true,
            dataRequestConfirm: 'Delete this author? Their posts stay, without an author.'
        ) }}
    </div>
</div>

<div class="control-tabs content-tabs" data-control="tab" data-linkable>
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#author">
                Author
            </a>
        </li>
        <li>
            <a href="#posts">
                Posts
            </a>
        </li>
        <li>
            <a href="#comments">
                Comments
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="grid">
                <div class="g-col-12 md:g-col-6">
                    <h4 class="my-5 fw-normal">
                        Account
                    </h4>
                    {!! $previewForm->renderTab('Account') !!}
                </div>
                <div class="g-col-12 md:g-col-6">
                    <h4 class="my-5 fw-normal">
                        Groups
                    </h4>
                    {!! $previewForm->renderTab('Groups') !!}
                </div>
            </div>
        </div>
        <div class="tab-pane">
            {{ $postToolbar }}
            {{ $postList }}
        </div>
        <div class="tab-pane">
            {{ $commentToolbar }}
            {{ $commentList }}
        </div>
    </div>
</div>

@endsection
