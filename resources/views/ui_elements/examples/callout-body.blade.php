{{ Ui::callout('<p>Reports now load twice as fast, and exports keep your column order.</p>')
    ->label('Release notes')
    ->comment('Version 2.1 is out.') }}

{{ Ui::callout(fn () => view('ui_elements._callout_checklist'))
    ->success()
    ->label('Setup complete') }}

{{ Ui::callout('<p>With a body and no label or comment, the callout has no header and no icon.</p>')->warning() }}
