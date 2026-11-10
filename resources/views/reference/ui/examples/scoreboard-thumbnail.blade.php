<?php Ui::scoreboard()->slot() ?>
    {{ Ui::scoreboardThumbnail(image: asset('images/desk-lamp.svg'), label: 'Desk lamp') }}
    {{ Ui::scoreboardItem(label: 'Product', value: 'Desk lamp', comment: 'SKU: LAMP-01') }}
    {{ Ui::scoreboardItem(label: 'In stock', value: 48, comment: 'reorder at 10') }}
<?php Ui::end() ?>
