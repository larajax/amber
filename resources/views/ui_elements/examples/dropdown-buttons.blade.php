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
    {{ Ui::dropdownItem(label: 'Open users', href: url('users'), icon: 'ph ph-users') }}
<?php Ui::end() ?>

<?php Ui::dropdownButton(label: 'More', icon: 'ph ph-dots-three-outline', secondary: true, caret: false)->slot() ?>
    {{ Ui::dropdownItem(label: 'Groups', href: url('user-groups')) }}
    {{ Ui::dropdownItem(label: 'Larajax docs', href: 'https://larajax.org', target: '_blank') }}
<?php Ui::end() ?>
