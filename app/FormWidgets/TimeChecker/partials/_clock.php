<div
    class="time-checker"
    data-control="timechecker"
    <?php if ($serverTimeMs): ?>data-server-time="<?= e($serverTimeMs) ?>"<?php endif ?>>
    <input
        type="hidden"
        name="<?= e($name) ?>"
        value="<?= e($value) ?>" />

    <dl class="time-checker-readings">
        <div class="time-checker-reading">
            <dt>
                Browser clock
            </dt>
            <dd data-timechecker-browser>
                --:--:--
            </dd>
        </div>
        <div class="time-checker-reading">
            <dt>
                <?= $isChecked ? 'Server clock' : 'Saved stamp' ?>
            </dt>
            <dd>
                <?= $stampLabel ? e($stampLabel) : 'Not checked yet' ?>
            </dd>
        </div>
        <div class="time-checker-reading">
            <dt>
                Drift
            </dt>
            <dd data-timechecker-drift>
                <?= $isChecked ? 'Measuring...' : 'Check the server first' ?>
            </dd>
        </div>
    </dl>

    <?php if (!$this->previewMode): ?>
        <?= Ui::ajaxButton(
            label: 'Check server time',
            handler: $this->getEventHandler('onCheckTime'),
            icon: 'ph ph-clock-clockwise',
            secondary: true
        ) ?>
    <?php endif ?>
</div>
