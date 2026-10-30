<div class="btn-group">
    {{ Ui::button(label: 'Day', secondary: true) }}
    {{ Ui::button(label: 'Week', secondary: true) }}
    {{ Ui::button(label: 'Month', secondary: true) }}
</div>

<div>
    {{ Ui::ajaxButton(label: 'Save draft', handler: 'onSaveDraft', primary: true, dataRequestFlash: true) }}
    <span class="button-text">
        or
        <a href="{{ route('reference.index') }}">
            Cancel
        </a>
    </span>
</div>
