<?php Ui::dropdownButton(label: 'Share', icon: 'ph ph-share-network', secondary: true)->slot() ?>
    {{ Ui::dropdownItem(
        label: 'Copy link',
        handler: 'onDropdownAction',
        icon: 'ph ph-link',
        requestData: ['action' => 'copy'],
        dataRequestFlash: true
    ) }}
    <?php Ui::dropdownItem(label: 'Export', icon: 'ph ph-export')->slot() ?>
        {{ Ui::dropdownItem(
            label: 'As PDF',
            handler: 'onDropdownAction',
            requestData: ['action' => 'pdf'],
            dataRequestFlash: true
        ) }}
        {{ Ui::dropdownItem(
            label: 'As CSV',
            handler: 'onDropdownAction',
            requestData: ['action' => 'csv'],
            dataRequestFlash: true
        ) }}
    <?php Ui::end() ?>
<?php Ui::end() ?>
