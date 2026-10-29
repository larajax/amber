<?php
    $visibleTags = $record->tags->take(3);
    $hiddenCount = $record->tags->count() - $visibleTags->count();
?>
<div class="d-flex flex-wrap gap-1">
    <?php foreach ($visibleTags as $tag): ?>
        <span class="badge badge-subtle theme-secondary">
            <?= e($tag->name) ?>
        </span>
    <?php endforeach ?>
    <?php if ($hiddenCount > 0): ?>
        <span class="badge badge-outline theme-secondary">
            +<?= $hiddenCount ?>
        </span>
    <?php endif ?>
</div>
