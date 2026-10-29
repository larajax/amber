<div data-control="toolbar">
    <?= Ui::button(
        label: 'New Post',
        href: 'posts/create',
        icon: 'ph ph-plus',
        primary: true
    ) ?>

    <?= Ui::popupButton(
        label: 'Quick Post',
        handler: 'onLoadQuickPost',
        icon: 'ph ph-lightning',
        hotkey: 'shift+q'
    ) ?>

    <?php Ui::dropdownButton(
        label: 'Bulk Actions',
        icon: 'ph ph-stack',
        dataListCheckedTrigger: true
    )->slot() ?>
        <?= Ui::dropdownItem(
            label: 'Publish',
            handler: 'onBulkPublish',
            icon: 'ph ph-eye',
            dataListCheckedRequest: true,
            dataRequestFlash: true
        ) ?>
        <?= Ui::dropdownItem(
            label: 'Unpublish',
            handler: 'onBulkUnpublish',
            icon: 'ph ph-eye-slash',
            dataListCheckedRequest: true,
            dataRequestFlash: true
        ) ?>
        <?= Ui::dropdownDivider() ?>
        <?= Ui::dropdownItem(
            label: 'Delete',
            handler: 'onBulkDelete',
            icon: 'ph ph-trash',
            dataListCheckedRequest: true,
            dataRequestFlash: true,
            dataRequestConfirm: 'Delete the checked posts?'
        ) ?>
    <?php Ui::end() ?>

    <?= Ui::button(
        label: 'Categories',
        href: 'categories',
        icon: 'ph ph-folders'
    ) ?>
</div>
