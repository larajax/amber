{{ Ui::callout()->icon('ph ph-rocket-launch')->label('New dashboard')->comment('Try the redesigned reports.') }}

{{ Ui::callout()->warning()->icon(false)->label('No icon')->comment('Pass false to leave the icon out.') }}

{{ Ui::callout()
    ->danger()
    ->label('Card expired')
    ->comment('Update your payment method to keep your plan.')
    ->action(Ui::ajaxButton(label: 'Update', handler: 'onCalloutAction', danger: true, dataRequestFlash: true)) }}
