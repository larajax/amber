{{ Ui::callout('<p>Reports now load twice as fast, and exports keep your column order.</p>')
    ->label('Release notes')
    ->comment('Version 2.1 is out.') }}

{{ Ui::callout(fn () => view('reference.ui._callout_checklist'))
    ->success()
    ->label('Setup complete') }}

{{ Ui::callout(Ui::button('Read the upgrade guide')->textLink()->linkUrl('https://larajax.org'))
    ->label('Upgrading')
    ->comment('The body can be another element, such as a fluent button.') }}

{{ Ui::callout('<p>With a body and no label or comment, the callout has no header and no icon.</p>')->warning() }}
