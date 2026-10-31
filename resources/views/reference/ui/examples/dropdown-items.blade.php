<?php Ui::dropdownButton(label: 'Project', secondary: true)->slot() ?>
    {{ Ui::dropdownItem(
        label: 'Rename',
        handler: 'onDropdownAction',
        icon: 'ph ph-pencil-simple',
        dataRequestData: "action: 'rename'",
        dataRequestFlash: true
    ) }}
    {{ Ui::dropdownItem(label: 'Export', icon: 'ph ph-download-simple', class: 'disabled', ariaDisabled: 'true') }}
    {{ Ui::dropdownDivider() }}
    {{ Ui::dropdownItem(
        label: 'Delete',
        handler: 'onDropdownAction',
        icon: 'ph ph-trash',
        class: 'theme-danger',
        dataRequestData: "action: 'delete'",
        dataRequestConfirm: 'Delete the project?',
        dataRequestFlash: true
    ) }}
<?php Ui::end() ?>
