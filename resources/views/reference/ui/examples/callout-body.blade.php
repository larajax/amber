<?php Ui::callout(label: 'Release notes', comment: 'Version 2.1 is out.')->slot() ?>
    <p>
        Reports now load twice as fast, and exports keep your column order.
    </p>
<?php Ui::end() ?>

<?php Ui::callout(label: 'Setup complete', success: true)->slot() ?>
    @include('reference.ui._callout_checklist')
<?php Ui::end() ?>

<?php Ui::callout(warning: true)->slot() ?>
    <p>
        With a body and no label or comment, the callout has no header and no icon.
    </p>
<?php Ui::end() ?>
