{{ Ui::popupButton(label: 'Adaptive', handler: 'onLoadDialog', secondary: true, requestData: ['size' => 'adaptive'], dataSize: 'adaptive') }}
{{ Ui::popupButton(
    label: 'Adaptive height',
    handler: 'onLoadDialog',
    secondary: true,
    requestData: ['size' => 'large'],
    dataSize: 'large',
    dataAdaptiveHeight: true
) }}
{{ Ui::popupButton(label: 'Fits the content', handler: 'onLoadDialog', secondary: true, requestData: ['size' => 'auto'], dataSize: 'auto') }}
{{ Ui::popupButton(label: '600 pixels', handler: 'onLoadDialog', secondary: true, requestData: ['size' => '600'], dataSize: '600') }}
{{ Ui::popupButton(label: 'Fullscreen', handler: 'onLoadDialog', secondary: true, requestData: ['size' => 'fullscreen'], dataSize: 'fullscreen') }}
