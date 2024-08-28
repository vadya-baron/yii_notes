<?php

use components\notes\models\Notes;

/** @var $model Notes */
$title = $title ?? $this->title;
$this->params['breadcrumbs'][] = $title;
?>
<div class="notes-index">
    <div class="body-content">
        <div class="row">
            <div class="col-md-12 mb-3">
                <h2><?= $model->getTitle();?></h2>

                <p><?= $model->getDescription();?></p>
                <p>
                    <a class="btn btn-lg btn-success mr-3" href="/notes/store">Добавить заметку</a>
                    <a class="btn btn-lg btn-success" href="/notes/store-tag">Добавить тег</a>
                </p>
                <?php if ($model->getCount()) : ?>
                    <hr class="mb-3">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="d-flex flex-column justify-content-center">
                                <?php foreach ($model->getItems() as $item) : ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading"><?= $item->getTitle(); ?></div>
                                        <div class="panel-body flex flex-row">
                                            <div class="description">
                                                <?= $item->getDescription(); ?>
                                            </div>
                                            <div class="tags">
                                                <?php if ($tags = $item->getTags()) : ?>
                                                    <?php foreach ($tags->getItems() as $tag) : ?>
                                                    <a href="/notes?tag=<?= $tag->getTitle(); ?>"
                                                       class="label label-primary"
                                                    >
                                                        <?= $tag->getTitle(); ?>
                                                    </a>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                            <hr>
                                            <div class="buttons">
                                                <a
                                                        class="btn btn-sm btn-secondary"
                                                        href="/notes/edit?id=<?= $item->getId(); ?>"
                                                >
                                                    Редактировать
                                                </a>
                                                <a
                                                        class="btn btn-sm btn-secondary"
                                                        href="/notes/delete?id=<?= $item->getId(); ?>"
                                                >
                                                    Удалить
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>
