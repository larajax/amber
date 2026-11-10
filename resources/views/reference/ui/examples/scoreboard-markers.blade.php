<?php Ui::scoreboard()->slot() ?>
    {{ Ui::scoreboardItem(label: 'Customers', value: '2,318', icon: 'ph ph-users', comment: '84 new this week') }}
    {{ Ui::scoreboardItem(label: 'Top seller', value: 'Desk lamp', icon: 'ph ph-star', color: 'var(--bs-warning-base)', comment: '312 sold') }}
    {{ Ui::scoreboardItem(label: 'Status', value: 'Shipped', color: 'var(--bs-success-base)', comment: 'since 3 Nov') }}
    {{ Ui::scoreboardItem(label: 'Supplier', value: 'Acme Supply', image: asset('images/acme-supply.svg'), comment: 'restocks weekly') }}
<?php Ui::end() ?>
