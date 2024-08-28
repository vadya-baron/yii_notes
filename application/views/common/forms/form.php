<?php

use commonComponents\models\forms\Form;

/** @var Form $form */
if (!$form) {
    return false;
}

?>
<form class="form" action="<?= $form->getAction(); ?>"
      method="<?= $form->getMethod() ?? 'post'; ?>"
      enctype="multipart/form-data">
    <fieldset>
        <?php if ($form->getHeader()): ?>
            <div class="page-caption">
                <h2><?= $form->getHeader(); ?></h2>
            </div>
        <?php endif ?>

        <?= $this->render('/common/errors', ['list' => $form->getErrors()]) ?>
        <?= $this->render('/common/messages', ['list' => $form->getMessages()]) ?>

        <?php foreach ($form->getFields() as $field): ?>
            <?= $this->render('/common/fields/' . $field->getType(), ['field' => $field]) ?>
        <?php endforeach ?>
        <div class="clear"></div>
    </fieldset>
    <?php if ($form->getButtons()): ?>
        <div class="buttons form-group">
            <?php foreach ($form->getButtons() as $button): ?>
                <?= $this->render('/common/buttons/button', ['button' => $button]) ?>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</form>
