<div data-control="toolbar">
    <?= Ui::popupButton(
        label: 'Preview',
        handler: 'onLoadToolbarDialog',
        icon: 'ph ph-app-window',
        hotkey: 'shift+q'
    ) ?>

    <?php Ui::dropdownButton(
        label: 'Bulk Actions',
        icon: 'ph ph-stack',
        dataListCheckedTrigger: true
    )->slot() ?>
        <?= Ui::dropdownItem(
            label: 'Count',
            handler: 'onCountCheckedPosts',
            icon: 'ph ph-calculator',
            dataListCheckedRequest: true,
            dataRequestFlash: true
        ) ?>
        <?= Ui::dropdownDivider() ?>
        <?= Ui::dropdownItem(
            label: 'Delete',
            handler: 'onDeleteCheckedPosts',
            icon: 'ph ph-trash',
            dataListCheckedRequest: true,
            dataRequestFlash: true,
            dataRequestConfirm: 'Delete the checked posts?'
        ) ?>
    <?php Ui::end() ?>

    <button
        type="button"
        class="btn"
        data-request="onCountCheckedPosts"
        data-request-flash
        data-list-checked-trigger
        data-list-checked-request>
        <i class="ph ph-check-square" aria-hidden="true"></i>
        Checked
        <span data-list-checked-counter></span>
    </button>
</div>
