<?php

use commonComponents\models\fields\Field;
use yii\helpers\Html;

/** @var Field $field */
if (!$field) {
    return false;
}
?>

<input
    id="<?= $field->getId() ?>"
    type="<?= $field->getType() ?>"
    name="<?= $field->getName() ?>"
    value="<?= Html::encode($field->getValue()) ?>"
    class="hidden"
/>