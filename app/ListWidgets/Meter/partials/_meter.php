<button
    type="button"
    class="list-meter"
    title="<?= e(number_format($value)) ?>"
    data-request="<?= e($this->getEventHandler('onShowValue')) ?>"
    data-request-data="<?= e(json_encode(['key' => $key])) ?>"
    data-request-flash="true"
>
    <span class="list-meter-bar" style="width: <?= $percent ?>%"></span>
</button>
