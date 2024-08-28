<?php

declare(strict_types=1);


namespace components\auth\filters;

use commonComponents\filters\Filter;

class UserFilter extends Filter
{
    public function __construct(
        protected ?int $id = null,
        protected ?string $username = null,
        protected ?string $email = null,
    ) {
    }

    public function addUsername(string $username): ?self
    {
        $this->username = $username;
        return $this;
    }

    public function addEmail(string $email): ?self
    {
        $this->email = $email;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}