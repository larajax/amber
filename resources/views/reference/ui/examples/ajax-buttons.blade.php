{{ Ui::ajaxButton(label: 'Flash a message', handler: 'onFlashMessage', primary: true, dataRequestFlash: true) }}
{{ Ui::ajaxButton(
    label: 'Archive item 7',
    handler: 'onArchiveItem',
    secondary: true,
    requestData: ['item' => 7],
    dataRequestFlash: true
) }}
{{ Ui::ajaxButton(
    label: 'Run after confirming',
    handler: 'onConfirmedAction',
    danger: true,
    dataRequestConfirm: 'Run this action?',
    dataRequestFlash: true
) }}
