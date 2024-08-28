<?php

namespace app\controllers;

use Yii;
use yii\web\Response;

class SiteController extends BaseController
{
    public function actionIndex(): string
    {
        $this->setTitle('Мои заметки');
        return $this->render('index');
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
//    public function actionContact()
//    {
//        $model = new ContactForm();
//        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
//            Yii::$app->session->setFlash('contactFormSubmitted');
//
//            return $this->refresh();
//        }
//        return $this->render('contact', [
//            'model' => $model,
//        ]);
//    }
}
