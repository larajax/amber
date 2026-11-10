<?php Ui::scoreboard()->slot() ?>
    {{ Ui::scoreboardItem(label: 'Orders', value: '1,284', comment: 'this month') }}
    {{ Ui::scoreboardItem(label: 'Average session', value: '4.2', unit: 'min', comment: 'this month') }}
    {{ Ui::scoreboardItem(label: 'Uptime', value: '99.98%', comment: 'last 30 days', success: true) }}
    {{ Ui::scoreboardItem(label: 'Failed jobs', value: 3, comment: 'since yesterday', danger: true) }}
    {{ Ui::scoreboardItem(label: 'Churn', value: null, comment: 'not enough data yet') }}
<?php Ui::end() ?>
