<?php

declare(strict_types=1);


namespace components\auth\entities;

use commonComponents\traits\Makeable;
use commonComponents\traits\ToArray;
use DateTime;

class Auth
{
    use Makeable;
    use ToArray;

    public function __construct(
        protected int $user_id,
        protected int|string $social_id,
        protected string $client,
        protected string $access_token,
        protected string $refresh_token,
        protected string $expired_date_time,
        protected ?string $device_id,
        protected ?int $id = null,
    ) {
    }

    public function addId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getSocialId(): int|string
    {
        return $this->social_id;
    }

    public function getClient(): string
    {
        return $this->client;
    }

    public function getAccessToken(): string
    {
        return $this->access_token;
    }

    public function getRefreshToken(): string
    {
        return $this->refresh_token;
    }

    public function getExpiresDateTime(): string
    {
        return $this->expired_date_time;
    }

    public function getDeviceId(): ?string
    {
        return $this->device_id;
    }

    public function isExpired(): bool
    {
        $format = 'Y-m-d H:i:s';
        $actualDate  = DateTime::createFromFormat($format, date($format));
        $expiresDate = DateTime::createFromFormat($format, $this->getExpiresDateTime());

        return $actualDate >= $expiresDate;
    }
}