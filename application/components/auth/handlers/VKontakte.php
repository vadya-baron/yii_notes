<?php

declare(strict_types=1);


namespace components\auth\handlers;

use components\auth\entities\Auth;
use components\auth\models\PKCEHash;
use Throwable;

class VKontakte extends OauthHandler
{
    public function __construct()
    {

    }
    protected string $authUrl = 'https://id.vk.com/authorize';
    protected string $tokenUrl = 'https://id.vk.com/oauth2/auth';
    protected string $userInfoUrl = 'https://id.vk.com/oauth2/user_info';

    public function configure(
        ?array $query = [],
        ?array $params = [],
    ): void {
        $this->query = $query;
        $this->params = $params;
    }

    public function geAuthUrl(?PKCEHash $hash = null): string
    {
        $query = [
            'client_id' => $this->params['clientId'] ?? null,
            'response_type' => 'code',
            'scope' => $this->params['scope'] ?? null,
            'redirect_uri' => $this->params['redirectUri'] ?? null,
            'state' => $this->params['state'] ?? null,
        ];

        if ($hash) {
            $query['code_verifier'] = $hash->getAuthCodeVerifier();
            $query['code_challenge'] = $hash->getCodeChallenge();
            $query['code_challenge_method'] = $hash->getCodeChallengeMethod();
        }

        return $this->authUrl . '?' . urldecode(http_build_query($query));
    }

    public function getAuthData(array $data, ?array &$errors = []): ?array
    {
        $headers = ['Content-Type' => 'application/x-www-form-urlencoded'];
        if ($query['code_verifier'] ?? null) {
            $headers['Origin'] = '/';
        }

        $responseAuth = $this->getData(
            uri: $this->tokenUrl,
            headers: $headers,
            data: $data,
            errors: $errors,
        );
        if (!$responseAuth) {
            $errors[] = 'Не удалось получить данные от сервиса авторизации';
            return null;
        }

        $error = $this->getError(response: $responseAuth);
        if ($error) {
            $errors[] = $error;
            return null;
        }

        $responseUser = $this->getData(
            uri: $this->userInfoUrl,
            headers: $headers,
            data: [
                'client_id' => $data['client_id'] ?? null,
                'access_token' => $responseAuth['access_token'] ?? null,
                'id_token' => $responseAuth['id_token'] ?? null,
                'scope' => $data['scope'] ?? null,
            ],
            errors: $errors,
        );
        if (!$responseUser) {
            $errors[] = 'Не удалось получить данные от сервиса авторизации';
            return null;
        }

        $error = $this->getError(response: $responseUser);
        if ($error) {
            $errors[] = $error;
            return null;
        }

        return array_merge(['device_id' => $data['device_id'] ?? null], $responseAuth, $responseUser);
    }

    public function getRefreshData(array $data, ?array &$errors = []): ?array
    {
        $headers = ['Content-Type' => 'application/x-www-form-urlencoded'];
        if ($query['code_verifier'] ?? null) {
            $headers['Origin'] = '/';
        }

        $responseRefreshAuth = $this->getData(
            uri: $this->tokenUrl,
            headers: $headers,
            data: $data,
            errors: $errors,
        );
        if (!$responseRefreshAuth) {
            $errors[] = 'Не удалось получить данные от сервиса авторизации';
            return null;
        }

        $error = $this->getError(response: $responseRefreshAuth);
        if ($error) {
            $errors[] = $error;
            return null;
        }

        return $responseRefreshAuth;
    }

    private function getError(array $response): ?string
    {
        $error = $response['error'] ?? null;
        if (!$error) {
            return null;
        }

        $errorDescription = $response['error_description'] ?? null;
        if ($error === 'invalid_request') {
            return match ($errorDescription) {
                'client_id is missing or invalid' => 'Идентификатор клиента отсутствует или недействителен.',
                'id_token is missing or invalid' => 'id_token отсутствует или недействителен.',
                default => 'Неверный запрос на авторизацию.'
            };
        }

        if ($error === 'invalid_client') {
            return 'Идентификатор клиента отсутствует или недействителен.';
        }

        if ($error === 'slow_down') {
            return 'Лимит запросов к методу превышен.';
        }

        if ($error === 'access_denied') {
            return 'У пользователя нет доступа к VK ID: пользователь заблокирован или удален.';
        }

        if ($error === 'invalid_id_token') {
            return 'id_token не передан, или не удалось его провалидировать.';
        }

        return 'Неизвестная ошибка авторизации.';
    }
}