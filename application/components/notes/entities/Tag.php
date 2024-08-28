<?php

declare(strict_types=1);


namespace components\notes\entities;

use commonComponents\traits\Makeable;
use commonComponents\traits\ToArray;

class Tag
{
    use Makeable;
    use ToArray;

    public function __construct(
        protected string $title,
        protected ?int $id = null,
    ) {
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

    public function getId(): ?int
    {
        return $this->id;
    }
}