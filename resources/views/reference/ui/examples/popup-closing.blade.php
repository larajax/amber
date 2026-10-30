{{ Ui::popupButton(
    label: 'Escape does not close',
    handler: 'onLoadDialog',
    secondary: true,
    dataKeyboard: 'false'
) }}
{{ Ui::popupButton(
    label: 'Backdrop click closes',
    handler: 'onLoadDialog',
    secondary: true,
    dataBackdrop: true
) }}
