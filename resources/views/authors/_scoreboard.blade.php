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
