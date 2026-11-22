<?php Ui::dropdownButton(label: 'View', icon: 'ph ph-eye', secondary: true)->slot() ?>
    {{ Ui::dropdownHeader(label: 'Density') }}
    {{ Ui::dropdownItem(
        label: 'Comfortable',
        handler: 'onDropdownAction',
        requestData: ['action' => 'comfortable'],
        selected: true,
        dataRequestFlash: true
    ) }}
    {{ Ui::dropdownItem(
        label: 'Compact',
        handler: 'onDropdownAction',
        requestData: ['action' => 'compact'],
        dataRequestFlash: true
    ) }}
    {{ Ui::dropdownDivider() }}
    {{ Ui::dropdownItem(
        label: 'Refresh',
        handler: 'onDropdownAction',
        icon: 'ph ph-arrows-clockwise',
        requestData: ['action' => 'refresh'],
        hotkey: 'shift+r',
        dataRequestFlash: true
    ) }}
    {{ Ui::dropdownItem(label: 'Details', handler: 'onLoadDialog', icon: 'ph ph-app-window', popup: true) }}
<?php Ui::end() ?>
