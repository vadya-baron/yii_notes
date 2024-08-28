<?php

use commonComponents\models\fields\Field;

/** @var Field $field */
if (!$field) {
    return false;
}
$note = $field->getNote();
$error = $field->getError();
$options = $field->getOptions();
?>
<div class="mb-3 <?= $field->isRequired() ? 'required' : ''; ?>">
    <label class="col-lg-12 col-form-label mr-lg-3" for="<?= $field->getId(); ?>">
        <?= $field->getLabel(); ?>
    </label>
    <select
            name="<?= $field->getName(); ?>"
            class="<?= $field->getClass() ?: 'col-lg-3 form-control js-select js-select-options'; ?>
            <?= $error ? ' is-invalid' : false; ?>"
            id="<?= $field->getId(); ?>"
            <?= $error ? 'aria-invalid="true"' : false; ?>
    >
        <?php if ($options): ?>
            <?php foreach ($options as $value => $title): ?>
                <option
                    value="<?= $value; ?>"
                    <?php if($field->getValue() == $value) echo 'selected="selected"'; ?>
                >
                    <?= $title ?>
                </option>
            <?php endforeach ?>
        <?php endif ?>
    </select>

    <?php if ($note) : ?>
        <span class="note"><?= $note; ?></span>
    <?php endif; ?>
    <?php if ($error) : ?>
        <div class="col-lg-7 invalid-feedback"><?= $error; ?></div>
    <?php endif; ?>
</div>
