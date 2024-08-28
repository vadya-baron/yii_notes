<?php

declare(strict_types=1);


namespace components\notes\models;

use commonComponents\traits\Makeable;

class Note
{
    use Makeable;

    public function __construct(
        protected int $id,
        protected string $title,
        protected string $description,
        protected int $userId,
        protected ?string $createAt = null,
        protected ?string $updateAt = null,
        protected ?Tags $tags = null,
    ) {
    }

    public function addTags(?Tags $tags): void
    {
        $this->tags = $tags;
    }

    public function getId(): int
    {
        return $this->id;
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
        return $this->userId;
    }

    public function getCreateAt(): ?string
    {
        return $this->createAt;
    }

    public function getUpdateAt(): ?string
    {
        return $this->updateAt;
    }

    public function getTags(): ?Tags
    {
        return $this->tags;
    }

    public function toArray(): array
    {
        $tags = $this->getTags();
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'userId' => $this->getUserId(),
            'tags' => $tags?->toArray(),
        ];
    }
}