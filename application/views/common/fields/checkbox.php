<?php

use commonComponents\models\fields\Field;

/** @var Field $field */
if (!$field) {
    return false;
}

$note = $field->getNote();
$error = $field->getError();

?>
<div class="checkbox mb-3 mr-3">
    <div class="d-flex">
        <input
            type="checkbox"
            id="<?= $field->getId(); ?>"
            class="float-left <?= $field->getClass(); ?>"
            name="<?= $field->getName(); ?>"
            value="<?= $field->getValue(); ?>"
            aria-required="<?= $field->isRequired() ? 'true' : 'false';?>"
            <?= $field->getData(); ?>
            <?php if($field->isCheck()) echo 'checked="checked"'; ?>
        >
        <label for="<?= $field->getId(); ?>">
            <?= $field->getLabel(); ?>
        </label>
    </div>
    <?php if ($note) : ?>
        <span class="note"><?= $note; ?></span>
    <?php endif; ?>
    <?php if ($error) : ?>
        <div class="col-lg-7 invalid-feedback"><?= $error; ?></div>
    <?php endif; ?>
</div>
