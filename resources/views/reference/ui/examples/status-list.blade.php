<?php Ui::statusList()->slot() ?>
    {{ Ui::statusItem(label: 'Software is up to date', value: 'Check again', href: '#status-list', icon: 'ph ph-check', success: true) }}
    {{ Ui::statusItem(label: 'Some issues need attention', value: 'View', href: '#status-list', icon: 'ph ph-warning', warning: true) }}
    {{ Ui::statusItem(label: 'System build', value: '313', icon: 'ph ph-info') }}
    {{ Ui::statusItem(label: 'Online since', value: '4 April 2026', muted: true) }}
<?php Ui::end() ?>
