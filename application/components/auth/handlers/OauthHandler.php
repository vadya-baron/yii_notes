<?php

declare(strict_types=1);


namespace components\auth\handlers;

use components\auth\entities\Auth;
use components\auth\models\PKCEHash;
use GuzzleHttp\Client;
use Throwable;

abstract class OauthHandler
{
    protected ?array $query = [];
    protected ?array $params = [];
    protected string $authUrl = '';
    protected string $tokenUrl = '';
    protected string $userInfoUrl = '';

    abstract public function configure(
        ?array $query = [],
        ?array $params = [],
    ): void;
    abstract public function geAuthUrl(?PKCEHash $hash = null): string;

    abstract public function getAuthData(array $data, ?array &$errors = []): ?array;
    abstract public function getRefreshData(array $data, ?array &$errors = []): ?array;

    protected function getData(string $uri, array $headers, array $data, ?array &$errors = []): ?array
    {
        $options = [
            'headers' => $headers,
            'form_params' => $data
        ];

        try {
            $client = new Client(['timeout' => 2, 'allow_redirects' => true]);
            $response = $client->request(
                method: 'POST',
                uri: $uri,
                options: $options,
            );
        } catch (Throwable $e) {
            $errors[] = 'Ошибка получения токена';
            /** TODO логирование ошибки @param $e */
            return null;
        }

        if ($response->getStatusCode() !== 200) {
            $errors[] = 'Ошибка получения токена';
            /** TODO логирование ошибки */
            return null;
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}