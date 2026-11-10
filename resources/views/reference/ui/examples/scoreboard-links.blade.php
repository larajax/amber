<?php Ui::scoreboard()->slot() ?>
    {{ Ui::scoreboardItem(
        label: 'Open orders',
        value: 18,
        comment: 'view the list',
        href: route('reference.ui.scoreboards').'#scoreboard-links'
    ) }}

    {{ Ui::scoreboardItem(
        label: 'Conversion rate',
        value: '3.4%',
        comment: 'last 30 days',
        tooltip: 'Orders placed divided by visits, over the last 30 days.'
    ) }}

    {{ Ui::scoreboardItem(
        label: 'Average order',
        value: '$86.40',
        comment: 'view the report',
        href: route('reference.ui.charts'),
        tooltip: 'Revenue divided by orders, before refunds.'
    ) }}
<?php Ui::end() ?>
