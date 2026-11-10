<?php Ui::scoreboard()->slot() ?>
    {{ Ui::scoreboardItem(label: 'Open rate', meter: 64, comment: '120 unread') }}
    {{ Ui::scoreboardItem(label: 'Goal', value: '37 of 50', meter: 74, comment: '13 posts to go') }}
    {{ Ui::scoreboardItem(label: 'Unsubscribe rate', meter: 3, inverse: true, comment: '12 opted out') }}
<?php Ui::end() ?>
