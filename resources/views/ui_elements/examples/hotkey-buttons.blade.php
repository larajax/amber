{{ Ui::ajaxButton(
    label: 'Save draft',
    handler: 'onSaveDraft',
    primary: true,
    hotkey: ['ctrl+s', 'cmd+s'],
    dataRequestFlash: true
) }}
{{ Ui::popupButton(label: 'Open a dialog', handler: 'onLoadDialog', secondary: true, hotkey: 'alt+shift+d') }}
{{ Ui::iconButton(
    label: 'Refresh',
    icon: 'ph ph-arrows-clockwise',
    handler: 'onRefreshData',
    hotkey: 'alt+shift+r',
    dataRequestFlash: true
) }}
