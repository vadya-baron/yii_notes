<?php

use commonComponents\models\fields\Field;

/** @var Field $field */
if (!$field) {
    return false;
}
$note = $field->getNote();
$error = $field->getError();
?>
<div class="mb-3 <?= $field->isRequired() ? 'required' : ''; ?>">
    <label class="col-lg-12 col-form-label mr-lg-3" for="<?= $field->getId(); ?>">
        <?= $field->getLabel(); ?>
    </label>
    <textarea
            type="text"
            id="<?= $field->getId(); ?>"
            class="<?= $field->getClass() ?: 'col-lg-3 form-control' ?><?= $error ? ' is-invalid' : false; ?>"
            name="<?= $field->getName(); ?>"
            aria-required="<?= $field->isRequired() ? 'true' : 'false';?>"
            placeholder="<?= $field->getPlaceholder() ?><?php if($field->getPlaceholder() && $field->isRequired()) echo ' *' ?>"
            <?= $field->getData() ?>
        <?= $error ? 'aria-invalid="true"' : false; ?>
    ><?= $field->getValue() ?></textarea>
    <?php if ($note) : ?>
        <span class="note"><?= $note; ?></span>
    <?php endif; ?>
    <?php if ($error) : ?>
        <div class="col-lg-7 invalid-feedback"><?= $error; ?></div>
    <?php endif; ?>
</div>

