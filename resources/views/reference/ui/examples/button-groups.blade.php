<?php Ui::buttonGroup()->slot() ?>
    {{ Ui::button(label: 'Day', secondary: true) }}
    {{ Ui::button(label: 'Week', secondary: true) }}
    {{ Ui::button(label: 'Month', secondary: true) }}
<?php Ui::end() ?>

<div>
    {{ Ui::ajaxButton(label: 'Save draft', handler: 'onSaveDraft', primary: true, dataRequestFlash: true) }}
    <span class="button-text">
        or
        <a href="{{ route('reference.index') }}">
            Cancel
        </a>
    </span>
</div>
