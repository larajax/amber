<?php /* A partial field rendering a dropdown that loads its options from the controller's onSearchTimezones handler */ ?>
<select
    id="<?= $field->getId() ?>"
    name="<?= $field->getName() ?>"
    class="form-control custom-select"
    data-handler="onSearchTimezones"
    data-minimum-input-length="2"
    data-ajax--delay="300"
    data-placeholder="Type two letters to search">
    <option value=""></option>
    <?php if ($formModel->timezone): ?>
        <option value="<?= e($formModel->timezone) ?>" selected>
            <?= e(str_replace('_', ' ', $formModel->timezone)) ?>
        </option>
    <?php endif ?>
</select>
