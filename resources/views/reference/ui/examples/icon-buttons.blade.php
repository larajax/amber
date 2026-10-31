{{ Ui::iconButton(label: 'Refresh', icon: 'ph ph-arrows-clockwise', handler: 'onRefreshData', dataRequestFlash: true) }}
{{ Ui::iconButton(
    label: 'Delete',
    icon: 'ph ph-trash',
    handler: 'onConfirmedAction',
    danger: true,
    dataRequestConfirm: 'Delete this item?',
    dataRequestFlash: true
) }}
