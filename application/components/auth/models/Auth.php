<?php

declare(strict_types=1);


namespace components\auth\models;

use commonComponents\traits\Makeable;
use components\auth\entities\User as UserEntity;
use components\auth\entities\Auth as AuthEntity;

class Auth
{
    use Makeable;

    protected array $clientParams = [];

    protected array $authData = [];
    protected ?UserEntity $user = null;
    protected ?AuthEntity $auth = null;

    public function __construct(
        protected array $params,
        protected ?array $query = [],
    ) {
    }

    public function addAuthData(array $authData): void
    {
        $this->authData = $authData;
    }

    public function getAuthData(): array
    {
        return $this->authData;
    }

    public function buildUser(?array $userData = null): UserEntity
    {
        if ($userData) {
            $this->user = UserEntity::make(...$userData);
        }
        if ($this->user) {
            return $this->user;
        }

        $name = $this->authData['user']['first_name'] ?? null;
        $name = $name . ' ' . $this->authData['user']['last_name'] ?? '';
        $this->user = UserEntity::make(
            username: $this->authData['user']['email'] ?? null,
            email: $this->authData['user']['email'] ?? null,
            name: trim($name),
        );

        return $this->user;
    }

    public function buildAuth(int $userId, ?array $authData = null): AuthEntity
    {
        if ($authData) {
            $this->auth = AuthEntity::make(...$authData);
        }
        if ($this->auth) {
            return $this->auth;
        }

        $this->auth = AuthEntity::make(
            user_id: $userId,
            social_id: $this->authData['user_id'] ?? null,
            client: $this->getClient(),
            access_token: $this->authData['access_token'] ?? null,
            refresh_token: $this->authData['refresh_token'] ?? null,
            expired_date_time: $this->getExpiredDateTime(),
            device_id: $this->authData['device_id'] ?? null,
        );

        return $this->auth;
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    public function getAuthorizationData(): ?array
    {
        if (!$this->authData) {
            return null;
        }
        return [
            'access_token' => $this->authData['access_token'] ?? null,
            'refresh_token' => $this->authData['refresh_token'] ?? null,
            'expires_in' => $this->authData['expires_in'] ?? null,
        ];
    }

    public function getClientParams(): array
    {
        if ($this->clientParams) {
            return $this->clientParams;
        }
        $this->clientParams = $this->params[$this->getClient()] ?? null;

        $baseUrl = $this->params['baseUrl'];
        $this->clientParams['redirectUri'] = $baseUrl . $this->clientParams['redirectUri'];
        $this->clientParams['state'] = $this->params['state'];
        return $this->clientParams;
    }

    public function getClient(): ?string
    {
        return $this->query['client'] ?? null;
    }

    public function getState(): string
    {
        return $this->params['state'] ?? '';
    }

    public function getAuthCode(): ?string
    {
        return $this->query['code'] ?? null;
    }

    public function getExpiresIn(): int
    {
        return (int)$this->query['expires_in'] ?? 0;
    }

    public function getExpiredDateTime(?int $expiresIn = null): string
    {
        if (!$expiresIn) {
            $expiresIn = (int)$this->query['expires_in'] ?? 0;
        }

        if (!$expiresIn) {
            return date('Y-m-d H:i:s', strtotime('+3600 sec'));
        }
        return date('Y-m-d H:i:s', strtotime("+{$expiresIn} sec"));
    }

    public function getDeviceId(): ?string
    {
        return $this->query['device_id'] ?? null;
    }

    public function getExtId(): ?string
    {
        return $this->query['ext_id'] ?? null;
    }

    public function getType(): ?string
    {
        return $this->query['type'] ?? null;
    }

    public function getSocialId(): int
    {
        return $this->query['user_id'] ?? 0;
    }
}