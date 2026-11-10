<?php Ui::scoreboard()->slot() ?>
    {{ Ui::scoreboardItem(
        label: 'Revenue',
        value: '$48,210',
        change: '14.9%',
        positive: true,
        sparkline: [36200, 38900, 37400, 41950, 43100, 45800, 48210],
        comment: 'previous month: $41,950'
    ) }}

    {{ Ui::scoreboardItem(
        label: 'Signups',
        value: 31,
        compare: [31, 42],
        sparkline: [48, 51, 44, 42, 39, 35, 31],
        comment: 'previous month: 42'
    ) }}

    {{ Ui::scoreboardItem(
        label: 'Visits',
        value: '12,408',
        change: '0.4%',
        sparkline: [12100, 12650, 12210, 12380, 12590, 12360, 12408],
        comment: 'about the same as last week'
    ) }}

    {{ Ui::scoreboardItem(
        label: 'Response time',
        value: 312,
        unit: 'ms',
        compare: [312, 340],
        inverse: true,
        sparkline: [355, 348, 362, 340, 331, 318, 312],
        comment: 'lower is better'
    ) }}
<?php Ui::end() ?>
