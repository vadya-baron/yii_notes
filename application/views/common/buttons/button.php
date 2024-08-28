<?php

use commonComponents\models\buttons\Button;

/** @var Button $button */
if (!$button) {
    return false;
}
$note = $button->getNote();
?>

<div class="d-flex flex-column">
    <button
            type="<?= $button->getType() ?>"
            name="<?= $button->getName() ?>"
            class="<?= $button->getClass() ?? 'btn' ?>"
        <?= $button->getData() ?>
    >
        <?= $button->getLabel() ?>
    </button>
    <?php if ($note) : ?>
    <span class="note"><?= $note; ?></span>
    <?php endif; ?>
</div>


