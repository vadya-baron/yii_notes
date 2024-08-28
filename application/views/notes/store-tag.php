<?php

use components\notes\models\TagForm;

/** @var $model TagForm */
if (!$model) {
    return false;
}
$title = $title ?? $this->title;
$this->params['breadcrumbs'][] = $title;
?>
<div class="tag-store">
    <div class="body-content">
        <div class="row">
            <div class="col-md-12 mb-3">
                <h2><?= $model->getTitle();?></h2>

                <p><?= $model->getDescription();?></p>

                <?= $this->render('/common/errors', ['list' => $model->getErrors()]) ?>
                <?= $this->render('/common/messages', ['list' => $model->getMessages()]) ?>

                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="d-flex flex-column justify-content-center">
                            <?= $this->render(
                                    '/common/forms/form',
                                    ['form' => $model->getForm()],
                            ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
