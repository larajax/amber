<?php Ui::dropdownButton(label: 'More', icon: 'ph ph-dots-three-outline', secondary: true, caret: false)->slot() ?>
    {{ Ui::dropdownItem(label: 'Popups', href: route('reference.ui.popups')) }}
    {{ Ui::dropdownItem(label: 'Larajax docs', href: 'https://larajax.org', target: '_blank') }}
<?php Ui::end() ?>
