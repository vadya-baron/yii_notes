<?php

namespace app\controllers;

use components\auth\interfaces\IAuth;
use Yii;
use yii\web\Response;

class AuthController extends BaseController
{
    protected IAuth $auth;
    protected array $paramsAuth;

    public function __construct(
        $id,
        $module,
        IAuth $auth,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->auth = $auth;
        $this->paramsAuth = Yii::$app->params['auth'] ?? [];
        $this->paramsAuth['baseUrl'] = str_replace(
            'http://',
            'https://',
            Yii::$app->getUrlManager()->getHostInfo(),
        );
    }

    public function actionLogin(): Response|string
    {
        if ($this->userId) {
            return $this->goHome();
        }

        $this->setTitle(title: 'Вход');

        return $this->render(
            'login',
            [
                'title' => 'Вход',
            ]
        );
    }

    public function actionOauth(): Response|string
    {
        if ($this->userId) {
            return $this->goHome();
        }
        if (!$this->validateOauthClient()) {
            return $this->throwBadRequest(message: 'Не передан клиент авторизации');
        }

        $errors = [];
        $this->auth->initAuth(
            state: Yii::$app->security->generateRandomString(),
            params: $this->paramsAuth,
            query: $this->getQuery(),
            errors: $errors,
        );

        if ($errors) {
            return $this->throwServerError(implode(', ', $errors));
        }

        $session = [
            'state' => $this->auth->getState(),
        ];
        if ($this->auth->isEnablePKCE()) {
            $codeVerifier = $this->auth->getAuthCodeVerifier(
                data: Yii::$app->security->generateRandomKey(64)
            );
            $session['codeVerifier'] = $codeVerifier;
        }

        $this->setOauthSession(
            client: $this->auth->getClient(),
            state: $this->auth->getState(),
            sessionData: $session,
        );

        return $this->redirect($this->auth->getAuthUrl());
    }

    public function actionVk(): Response|string
    {
        if ($this->userId) {
            return $this->goHome();
        }
        $errors = [];

        $this->auth->initAuth(
            state: $this->getQueryByKey(key: 'state'),
            params: $this->paramsAuth,
            query: $this->getQuery(),
            errors: $errors,
        );

        if ($errors) {
            return $this->throwServerError(implode(', ', $errors));
        }

        $session = $this->getOauthSession(
            client: $this->auth->getClient(),
            state: $this->auth->getState(),
        );

        $this->removeOauthSession(
            client: $this->auth->getClient(),
            state: $this->auth->getState(),
        );

        if (!$this->auth->verify(
            session: $session,
            errors: $errors,
        )) {
            return $this->throwDenied(implode(', ', $errors));
        }

        $this->auth->auth(
            codeVerifier: $session['codeVerifier'] ?? null,
            errors: $errors,
        );
        if ($errors) {
            return $this->throwServerError(implode(', ', $errors));
        }

        Yii::$app->user->login($this->auth->getIdentity());
        return $this->goHome();
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    private function validateOauthClient(): bool
    {
        $client = $this->getQueryByKey(key: 'client');
        if (!$client || is_array($client)) {
            return false;
        }

        return array_key_exists($client, $this->paramsAuth);
    }

    private function setOauthSession(
        string $client,
        string $state,
        array $sessionData,
    ): void {
        Yii::$app->session->set(
            'oauth.client.' . $client . '.' . $state,
            $sessionData,
        );
    }

    private function getOauthSession(
        string $client,
        string $state,
    ): array {
        $data = Yii::$app->session->get(
            'oauth.client.' . $client . '.' . $state,
        );

        if (!$data) {
            return [];
        }

        return $data;
    }

    private function removeOauthSession(
        string $client,
        string $state,
    ): void {
        Yii::$app->session->set(
            'oauth.client.' . $client . '.' . $state,
            null,
        );
    }
}
