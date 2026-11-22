<?php Ui::splitButton(
    label: 'Save',
    handler: 'onDropdownAction',
    requestData: ['action' => 'save'],
    primary: true,
    dataRequestFlash: true
)->slot() ?>
    {{ Ui::dropdownItem(
        label: 'Save and close',
        handler: 'onDropdownAction',
        requestData: ['action' => 'save-close'],
        dataRequestFlash: true
    ) }}
    {{ Ui::dropdownItem(
        label: 'Save as draft',
        handler: 'onDropdownAction',
        requestData: ['action' => 'draft'],
        dataRequestFlash: true
    ) }}
<?php Ui::end() ?>
