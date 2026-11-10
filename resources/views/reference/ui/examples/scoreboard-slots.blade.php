<?php Ui::scoreboard()->slot() ?>
    <?php Ui::scoreboardItem(label: 'Customer')->slot() ?>
        <a href="{{ route('reference.ui.scoreboards') }}#scoreboard-slots">
            Jane Doe
        </a>
    <?php Ui::slot('comment') ?>
        Email:
        <a href="mailto:jane@example.com">
            jane@example.com
        </a>
    <?php Ui::end() ?>

    <?php Ui::scoreboardItem(label: 'Total', value: '$1,240.00')->slot('comment') ?>
        Tax: $112.73 &bull; Shipping: $15.00
    <?php Ui::end() ?>
<?php Ui::end() ?>
