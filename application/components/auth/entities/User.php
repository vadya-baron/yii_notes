<?php

declare(strict_types=1);


namespace components\auth\entities;

use commonComponents\traits\Makeable;
use commonComponents\traits\StringHash;
use commonComponents\traits\ToArray;

class User
{
    use Makeable;
    use ToArray;
    use StringHash;

    const USER_STATUS_INACTIVE = 0;
    const USER_STATUS_ACTIVE = 1;

    public function __construct(
        protected string $username,
        protected string $email,
        protected ?int $id = null,
        protected ?string $auth_key = null,
        protected ?string $name = null,
        protected ?string $password_hash = null,
        protected ?string $password_reset_token = null,
        protected ?int $status = self::USER_STATUS_ACTIVE,
        protected ?string $create_at = null,
        protected ?string $update_at = null,
    ) {
        $date = date('Y-m-d H:i:s');
        $key = $this->getHash(
            data: $username . $email . 'auth_key' . $date
        );

        if (!$auth_key) {
            $this->auth_key = $key;
        }

        if (!$password_hash) {
            $this->password_hash = password_hash(
                $key,
                PASSWORD_DEFAULT,
                ['cost' => 12]
            );
        }

        if (!$password_reset_token) {
            $this->password_reset_token = $this->getHash(
                data: $key . 'password_reset_token'
            );
        }

        if (!$create_at) {
            $this->create_at = $date;
        }

        if (!$update_at) {
            $this->update_at = $date;
        }
    }

    public function addId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): int
    {
        return $this->id ?? 0;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    public function getPasswordHash(): string
    {
        return $this->password_hash;
    }

    public function getPasswordResetToken(): string
    {
        return $this->password_reset_token;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getCreateAt(): string
    {
        return $this->create_at;
    }

    public function getUpdateAt(): string
    {
        return $this->update_at;
    }
}