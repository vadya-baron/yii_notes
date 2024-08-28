<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$title = $title ?? $this->title;
$this->params['breadcrumbs'][] = $title;
/** @var $title */

?>
<div class="site-login">
    <h1><?= $title; ?></h1>

    <?php if ($model ?? null) : ?>
        <div class="row">
            <div class="col-lg-5">

                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'fieldConfig' => [
                        'template' => "{label}\n{input}\n{error}",
                        'labelOptions' => ['class' => 'col-lg-1 col-form-label mr-lg-3'],
                        'inputOptions' => ['class' => 'col-lg-3 form-control'],
                        'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
                    ],
                ]); ?>

                <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <?= $form->field($model, 'rememberMe')->checkbox([
                    'template' => "<div class=\"custom-control custom-checkbox\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
                ]) ?>

                <div class="form-group">
                    <div>
                        <?= Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    <?php endif;?>
    <div class="row">
        <div class="col-lg-5">
            <h4>Войти с помощью социальных сетей</h4>
            <ul class="auth-clients">
                <li>
                    <a
                        class="vkontakte auth-link"
                        href="/auth/oauth?client=vkontakte"
                        title="VKontakte"
                    >
                        <span class="auth-icon vkontakte"></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
