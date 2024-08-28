<?php

declare(strict_types=1);


namespace components\notes\filters;

use commonComponents\filters\Filter;

class TagFilter extends Filter
{
    /**
     * @param array<int>|null $ids
     */
    public function __construct(
        protected ?int $id = null,
        protected ?array $ids = null,
        protected ?string $title = null,
    ) {
    }

    public function addTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }
}