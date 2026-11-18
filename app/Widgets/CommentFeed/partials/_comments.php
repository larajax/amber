<?php if ($comments->isEmpty()): ?>
    <p class="comment-feed-empty">
        No comments yet.
    </p>
<?php else: ?>
    <ol class="comment-feed-list">
        <?php foreach ($comments as $comment): ?>
            <li class="comment-feed-item">
                <div class="comment-feed-meta">
                    <strong>
                        <?= e($comment->author) ?>
                    </strong>
                    <?php if ($comment->created_at): ?>
                        <time datetime="<?= e($comment->created_at->toIso8601String()) ?>">
                            <?= e($comment->created_at->diffForHumans()) ?>
                        </time>
                    <?php endif ?>
                </div>
                <p>
                    <?= e($comment->content) ?>
                </p>
            </li>
        <?php endforeach ?>
    </ol>
<?php endif ?>

<?php if ($remaining > 0): ?>
    <?= Ui::ajaxButton(
        label: "Show more ({$remaining} left)",
        handler: $this->getEventHandler('onLoadMore'),
        requestData: ['shown' => $comments->count()],
        icon: 'ph ph-caret-down',
        secondary: true
    ) ?>
<?php endif ?>
