<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class BaseController extends Controller
{
    protected int $userId;
    protected string $appName;
    public function __construct(
        $id,
        $module,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->request = Yii::$app->request;
        $this->userId = Yii::$app->user?->id ?? 0;
        $this->appName = Yii::$app->name;
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    protected function setTitle(string $title): void
    {
        $this->view->title = $this->appName . ' :: ' . $title;
    }

    protected function getQuery(): array
    {
        return $this->normalizeQuery($this->request->getQueryParams());
    }

    protected function getQueryByKey(string $key): null|array|string
    {
        $param = $this->request->getQueryParam($key);
        if (!$param) {
            return null;
        }
        if (is_array($param)) {
            return $this->normalizeQuery($param);
        }
        return $this->normalizeValue((string)$param);
    }

    protected function getPost(): array
    {
        return $this->normalizeQuery($this->request->post());
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function throwNotFound(?string $message = null): NotFoundHttpException
    {
        if (!$message) {
            $message = 'Страница не найдена.';
        }
        throw new NotFoundHttpException($message);
    }

    /**
     * @throws ForbiddenHttpException
     */
    protected function throwDenied(?string $message = null): ForbiddenHttpException
    {
        if (!$message) {
            $message = 'Нет доступа.';
        }
        throw new ForbiddenHttpException($message);
    }

    /**
     * @throws BadRequestHttpException
     */
    protected function throwBadRequest(?string $message = null): BadRequestHttpException
    {
        if (!$message) {
            $message = 'Неверные данные.';
        }
        throw new BadRequestHttpException($message);
    }

    /**
     * @throws ServerErrorHttpException
     */
    protected function throwServerError(?string $message = null): ServerErrorHttpException
    {
        if (!$message) {
            $message = 'Что-то пошло не так, попробуйте зайти позже.';
        }
        throw new ServerErrorHttpException($message);
    }

    protected function logErrors($errors): void
    {
        foreach ($errors as $error) {
            Yii::info($error, 'site-error');
        }
    }

    private function normalizeQuery(array $query): array
    {
        if (!$query) {
            return [];
        }
        foreach ($query as $key => $value) {
            if (is_array($value)) {
                $value = $this->normalizeQuery($value);
                $query[$key] = $value;
            } else {
                $query[$key] = $this->normalizeValue(value: (string)$value);
            }
        }

        return $query;
    }

    private function normalizeValue(string $value): string
    {
        return trim(htmlentities(strip_tags($value)));
    }
}
