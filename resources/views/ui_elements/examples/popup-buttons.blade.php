{{ Ui::popupButton(label: 'Open a dialog', handler: 'onLoadDialog', primary: true) }}
{{ Ui::popupButton(label: 'Tiny', handler: 'onLoadDialog', secondary: true, requestData: ['size' => 'tiny'], dataSize: 'tiny') }}
{{ Ui::popupButton(label: 'Small', handler: 'onLoadDialog', secondary: true, requestData: ['size' => 'small'], dataSize: 'small') }}
{{ Ui::popupButton(label: 'Large', handler: 'onLoadDialog', secondary: true, requestData: ['size' => 'large'], dataSize: 'large') }}
{{ Ui::popupButton(label: 'Huge', handler: 'onLoadDialog', secondary: true, requestData: ['size' => 'huge'], dataSize: 'huge') }}
