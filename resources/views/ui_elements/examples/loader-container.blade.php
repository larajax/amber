<div class="d-flex flex-wrap gap-3" data-control="loader-container">
    {{ Ui::ajaxButton(
        label: 'Run a slow request',
        handler: 'onSlowRequest',
        primary: true,
        dataRequestFlash: true
    ) }}
    {{ Ui::button(label: 'Blocked while loading', secondary: true) }}
</div>
