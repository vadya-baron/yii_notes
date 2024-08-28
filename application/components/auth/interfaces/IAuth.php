<?php

declare(strict_types=1);


namespace components\auth\interfaces;


use components\auth\models\PKCEHash;
use yii\web\IdentityInterface;

interface IAuth
{
    public function initAuth(
        string $state,
        array $params,
        ?array $query = [],
        ?array &$errors = [],
    ): void;

    public function getState(): string;

    public function getAuthUrl(): string;

    public function getClient(): ?string;

    public function isEnablePKCE(): bool;

    public function getAuthCodeVerifier(
        string $data,
        string $algo = 'sha256',
    ): string;

    public function verify(
        array $session = [],
        ?array &$errors = [],
    ): bool;

    public function auth(
        ?string $codeVerifier = null,
        ?array &$errors = [],
    ): void;

    public function getIdentity(?array &$errors = []): ?IdentityInterface;

    public function getAuthorizationData(): ?array;
}