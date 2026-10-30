<?php Ui::dropdownButton(label: 'Actions', primary: true)->slot() ?>
    {{ Ui::dropdownItem(
        label: 'Duplicate',
        handler: 'onDropdownAction',
        icon: 'ph ph-copy',
        dataRequestData: "action: 'duplicate'",
        dataRequestFlash: true
    ) }}
    {{ Ui::dropdownItem(
        label: 'Archive',
        handler: 'onDropdownAction',
        icon: 'ph ph-archive',
        dataRequestData: "action: 'archive'",
        dataRequestFlash: true
    ) }}
    {{ Ui::dropdownDivider() }}
    {{ Ui::dropdownItem(label: 'Open the reference', href: route('reference.index'), icon: 'ph ph-book-open') }}
<?php Ui::end() ?>
