<?php /* A partial field that reads the form model, showing which record the form edits and when it was last saved */ ?>
<div class="reference-forms-inline">
    <strong>
        <?= e($formModel->name) ?>
    </strong>
    <span class="badge theme-secondary">
        <?= $formModel->exists ? 'Record '.e($formModel->getKey()) : 'New record' ?>
    </span>
    <span class="fg-3">
        <?= $formModel->updated_at ? 'Saved '.e($formModel->updated_at->diffForHumans()) : 'Not saved yet' ?>
    </span>
</div>
