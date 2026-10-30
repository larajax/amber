<?php if ($value && $value->count()): ?>
    <div class="d-flex flex-wrap gap-1">
        <?php foreach ($value as $tag): ?>
            <span class="badge badge-subtle theme-secondary">
                <?= e($tag->name) ?>
            </span>
        <?php endforeach ?>
    </div>
<?php else: ?>
    <span class="fg-3">
        Untagged
    </span>
<?php endif ?>
