<?php Ui::scoreboard()->slot() ?>
    {{ Ui::pieChart(values: [
        'Published' => [84, 'var(--bs-success-base)'],
        'Drafts' => [12, 'var(--bs-warning-base)'],
        'Deleted' => [18, 'var(--bs-danger-base)'],
    ]) }}

    {{ Ui::barChart(values: [
        'Published' => [84, 'var(--bs-success-base)'],
        'Drafts' => [12, 'var(--bs-warning-base)'],
        'Deleted' => [18, 'var(--bs-danger-base)'],
    ]) }}

    {{ Ui::scoreboardItem(label: 'Posts', value: 114, comment: 'all time') }}
<?php Ui::end() ?>
