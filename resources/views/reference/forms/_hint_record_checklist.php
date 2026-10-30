<ul class="mb-0">
    <li>
        <?= e($formModel->name ?: 'This record') ?> needs a price before it can go on sale.
    </li>
    <li>
        The slug <code><?= e($formModel->slug ?: 'not set') ?></code> becomes part of its address.
    </li>
</ul>
