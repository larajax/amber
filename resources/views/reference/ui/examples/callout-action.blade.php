<?php Ui::callout(label: 'Trial ending', comment: 'Your trial ends in 3 days.', warning: true)->slot() ?>
    <p>
        Add a payment method to keep your projects. Everything you made during the trial stays.
    </p>
<?php Ui::slot('action') ?>
    {{ Ui::ajaxButton(label: 'Add a card', handler: 'onCalloutAction', primary: true, dataRequestFlash: true) }}
<?php Ui::end() ?>

<?php Ui::callout(label: 'Card expired', comment: 'Update your payment method to keep your plan.', danger: true)->slot('action') ?>
    {{ Ui::ajaxButton(label: 'Update', handler: 'onCalloutAction', danger: true, dataRequestFlash: true) }}
<?php Ui::end() ?>
