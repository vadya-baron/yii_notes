<?php

declare(strict_types=1);


namespace components\auth\filters;

use commonComponents\filters\Filter;

class AuthFilter extends Filter
{
    public function __construct(
        protected int $userId,
        protected ?int $socialId = null,
        protected ?string $client = null,
    ) {
    }

    public function addSocialId(int $socialId): ?self
    {
        $this->socialId = $socialId;
        return $this;
    }

    public function addClient(string $client): ?self
    {
        $this->client = $client;
        return $this;
    }

    public function getSocialId(): ?int
    {
        return $this->socialId;
    }

    public function getClient(): ?string
    {
        return $this->client;
    }
}