{{ Ui::ajaxButton('Archive item 3', 'onArchiveItem')
    ->ajaxData(['item' => 3])
    ->confirmMessage('Archive item 3?')
    ->attachLoading(true)
    ->attributes(['data-request-flash' => true]) }}
{{ Ui::popupButton('Small dialog', 'onLoadDialog')
    ->size('small')
    ->ajaxData(['size' => 'small']) }}
{{ Ui::ajaxButton('Delete', 'onConfirmedAction')
    ->formDeleteButton()
    ->confirmMessage('Delete this item?')
    ->attributes(['data-request-flash' => true]) }}
