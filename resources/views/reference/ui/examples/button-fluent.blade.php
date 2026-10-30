{{ Ui::button(label: 'Publish')->primary()->icon('ph ph-paper-plane-tilt') }}
{{ Ui::ajaxButton(label: 'Archive item 7', dataRequestFlash: true)
    ->handler('onArchiveItem')
    ->requestData(['item' => 7])
    ->secondary() }}
