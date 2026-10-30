<?php Ui::callout()->warning()->label('Trial ending')->comment('Your trial ends in 3 days.')->slot() ?>
    <p>
        Add a payment method to keep your projects. Everything you made during the trial stays.
    </p>
<?php Ui::slot('action') ?>
    {{ Ui::ajaxButton(label: 'Add a card', handler: 'onCalloutAction', primary: true, dataRequestFlash: true) }}
<?php Ui::end() ?>
