@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

<h1 class="mb-3">
    Dashboard
</h1>
<p class="fg-3 mb-7">
    The blog at a glance. Hover a chart or a sparkline for its values, and click a figure or a post to open it.
    <a href="{{ config('reference.docs_url') }}/ui/scoreboards" target="_blank" rel="noopener">
        How this page is built
    </a>
</p>

<?php Ui::scoreboard()->slot() ?>
    {{ Ui::pieChart(values: $statusValues) }}

    {{ Ui::scoreboardItem(
        label: 'Published',
        value: $published['count'],
        compare: $published['compare'],
        sparkline: $published['sparkline'],
        comment: $published['compare'][0].' in the last 30 days',
        href: route('posts.index'),
        tooltip: 'Posts published in the last 30 days, against the 30 days before, with a line of the last eight weeks.'
    ) }}

    {{ Ui::scoreboardItem(
        label: 'Views',
        value: number_format($views['total']),
        sparkline: $views['sparkline'],
        comment: number_format($views['average']).' a post on average'
    ) }}

    {{ Ui::scoreboardItem(
        label: 'Comments',
        value: $comments['count'],
        icon: 'ph ph-chat-circle-dots',
        color: 'var(--bs-cyan-500)',
        comment: $comments['removed'].' removed'
    ) }}

    {{ Ui::scoreboardItem(
        label: 'Monthly goal',
        value: $goal['count'].' of '.$goal['target'],
        meter: $goal['meter'],
        comment: $goal['count'] >= $goal['target'] ? 'goal reached' : ($goal['target'] - $goal['count']).' to publish this month'
    ) }}
<?php Ui::end() ?>

<div class="grid mt-7">
    <section class="card g-col-12 lg:g-col-8">
        <h2 class="card-header h6 mb-0">
            Views by publish week
        </h2>
        <div class="card-body">
            {{ Ui::lineChart(height: 240, datasets: [
                'Views' => ['color' => 'var(--bs-blue-500)', 'data' => $weeklyViews],
            ]) }}
        </div>
    </section>

    <section class="card g-col-12 lg:g-col-4">
        <h2 class="card-header h6 mb-0">
            Posts by section
        </h2>
        <div class="card-body">
            {{ Ui::barChart(values: $sectionValues, height: 160, fullWidth: true) }}
        </div>
    </section>

    <section class="card g-col-12 lg:g-col-8">
        <h2 class="card-header h6 mb-0">
            Most read
        </h2>
        {{ $popularPosts }}
    </section>

    <section class="card g-col-12 lg:g-col-4">
        <h2 class="card-header h6 mb-0">
            Needs attention
        </h2>
        <div class="card-body">
            <?php Ui::statusList()->slot() ?>
                @if ($attention['inReview'])
                    {{ Ui::statusItem(
                        label: $attention['inReview'].' '.Str::plural('post', $attention['inReview']).' waiting for review',
                        value: 'View',
                        href: route('posts.index'),
                        icon: 'ph ph-eye',
                        warning: true
                    ) }}
                @else
                    {{ Ui::statusItem(label: 'Nothing is waiting for review', icon: 'ph ph-check', success: true) }}
                @endif

                {{ Ui::statusItem(
                    label: $attention['reviewsDue'].' '.Str::plural('review', $attention['reviewsDue']).' due in the next two weeks',
                    icon: 'ph ph-calendar-check',
                    info: true
                ) }}

                {{ Ui::statusItem(
                    label: $attention['drafts'].' '.Str::plural('draft', $attention['drafts']).' in progress',
                    icon: 'ph ph-pencil-simple',
                    primary: true
                ) }}

                {{ Ui::statusItem(
                    label: $attention['withoutImage'].' '.Str::plural('post', $attention['withoutImage']).' without a featured image',
                    icon: 'ph ph-image'
                ) }}

                @if ($attention['lastPublished'])
                    {{ Ui::statusItem(
                        label: 'Last published',
                        value: $attention['lastPublished']->published_at->diffForHumans(),
                        href: route('posts.edit', $attention['lastPublished']),
                        muted: true
                    ) }}
                @endif
            <?php Ui::end() ?>
        </div>
    </section>
</div>

@endsection
