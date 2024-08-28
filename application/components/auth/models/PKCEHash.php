<?php

declare(strict_types=1);


namespace components\auth\models;

use commonComponents\traits\Makeable;
use components\auth\exceptions\AuthHandlerException;
use components\auth\handlers\OauthHandler;
use Throwable;

class PKCEHash
{
    use Makeable;

    protected string $authCodeVerifier;
    protected string $codeChallenge;
    protected string $codeChallengeMethod;

    public function __construct(
        protected string $data,
        protected string $algo = 'sha256',
    ) {
        $this->authCodeVerifier = bin2hex($data);
        $this->codeChallenge = trim(
            strtr(
                base64_encode(hash($algo, $this->authCodeVerifier, true)),
                '+/', '-_'
            ),
            '='
        );
        $this->codeChallengeMethod = match ($algo) {
            'sha256' => 'S256',
            default => 'S128',
        };
    }

    public function getAuthCodeVerifier(): string
    {
        return $this->authCodeVerifier;
    }

    public function getCodeChallenge(): string
    {
        return $this->codeChallenge;
    }

    public function getCodeChallengeMethod(): string
    {
        return $this->codeChallengeMethod;
    }
}