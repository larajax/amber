<?php
    /* A partial field that reads the headline from the form model, refreshed by the headline's change handler */
    $headline = trim((string) $formModel->headline);
    $characters = mb_strlen($headline);
    $words = $headline === '' ? 0 : count(preg_split('/\s+/u', $headline));

    if ($characters === 0) {
        $verdict = ['fg-3', 'Type a headline and leave the field to measure it.'];
    }
    elseif ($characters > 60) {
        $verdict = ['fg-danger', 'Too long, search results cut titles after about 60 characters.'];
    }
    elseif ($characters < 20) {
        $verdict = ['fg-warning', 'A little short, aim for 20 to 60 characters.'];
    }
    else {
        $verdict = ['fg-success', 'A good length for a title.'];
    }
?>
<div class="kitchen-sink-stats">
    <span class="badge theme-secondary">
        <?= $characters ?> characters
    </span>
    <span class="badge theme-secondary">
        <?= $words ?> words
    </span>
    <span class="<?= $verdict[0] ?>">
        <?= e($verdict[1]) ?>
    </span>
</div>
