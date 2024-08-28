<?php

declare(strict_types=1);


namespace components\notes\entities;

use commonComponents\traits\Makeable;
use commonComponents\traits\ToArray;

class Note
{
    use Makeable;
    use ToArray;

    public function __construct(
        protected string $title,
        protected string $description,
        protected int $user_id,
        protected ?int $id = null,
        protected ?string $create_at = null,
        protected ?string $update_at = null,
    ) {
        $date = date('Y-m-d H:i:s');
        if (!$create_at) {
            $this->create_at = $date;
        }
        if (!$this->update_at) {
            $this->update_at = $date;
        }
    }

    public function addId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getId(): ?int
    {
        return $this->id;
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