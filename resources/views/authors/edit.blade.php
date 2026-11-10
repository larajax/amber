@extends('layouts.app')
@section('title', 'Edit Author')
@section('content')

<form data-request="onUpdate" data-request-flash>
    <div class="d-flex align-items-center justify-content-between mb-7">
        <h1 class="mb-0">
            Edit Author
        </h1>
        <a href="{{ route('authors.index') }}" class="btn-link text-decoration-none">
            &larr; Back to authors
        </a>
    </div>

    <?php Ui::scoreboard(class: 'mb-7')->slot() ?>
        {{ Ui::scoreboardItem(
            label: 'Posts',
            value: $figures['postCount'],
            icon: 'ph ph-article',
            color: 'var(--bs-indigo-500)',
            comment: $figures['publishedCount'].' published'
        ) }}

        {{ Ui::scoreboardItem(
            label: 'Views',
            value: $figures['views'] ? number_format($figures['views']) : null,
            sparkline: $figures['viewsByPost'],
            comment: $figures['views'] ? number_format($figures['averageViews']).' a post on average' : 'nothing published yet'
        ) }}

        {{ Ui::scoreboardItem(
            label: 'Comments',
            value: $figures['comments'],
            comment: 'on their posts'
        ) }}

        @if ($figures['latestPost'])
            {{ Ui::scoreboardItem(
                label: 'Latest post',
                value: $figures['latestPost']->published_at->format('j M'),
                comment: Str::limit($figures['latestPost']->title, 32),
                href: route('posts.edit', $figures['latestPost'])
            ) }}
        @endif
    <?php Ui::end() ?>

    {{ $formWidget }}

    <div class="mt-7 d-flex gap-3">
        {{ Ui::ajaxButton(dataRequestFlash: true, handler: 'onUpdate', label: 'Save', primary: true) }}
        {{ Ui::ajaxButton(
            dataRequestFlash: true,
            handler: 'onDestroy',
            label: 'Delete',
            danger: true,
            dataRequestConfirm: 'Delete this author? Their posts stay, without an author.'
        ) }}
    </div>
</form>

@endsection
