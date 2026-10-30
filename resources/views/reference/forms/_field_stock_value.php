<?php
    /* A partial field showing the price times the quantity, with a button that asks the form's onRefresh handler to redraw this field */
    $stockValue = (float) $formModel->price * (int) $formModel->quantity;
?>
<div class="reference-forms-inline">
    <strong>
        $<?= number_format($stockValue, 2) ?>
    </strong>
    <span class="fg-3">
        <?= (int) $formModel->quantity ?> at $<?= number_format((float) $formModel->price, 2) ?>
    </span>
    <?= Ui::ajaxButton(
        label: 'Recalculate',
        handler: 'exampleRefresh::onRefresh',
        icon: 'ph ph-arrows-clockwise',
        requestData: ['fields' => ['_stock_value']],
    ) ?>
</div>
