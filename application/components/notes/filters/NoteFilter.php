<?php

namespace components\notes\filters;

use commonComponents\filters\Filter;

class NoteFilter extends Filter
{
    public function __construct(
        protected int $userId,
        protected ?int $id = null,
        protected ?array $ids = null,
        protected ?string $sort = null,
        protected ?string $search = null,
        protected ?string $tagTitle = null,
    ) {
    }

    public function addTagTitle(string $tagTitle): ?self
    {
        $this->tagTitle = $tagTitle;
        return $this;
    }

    public function addSearch(string $search): ?self
    {
        $this->search = $search;
        return $this;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function getTagTitle(): ?string
    {
        return $this->tagTitle;
    }
}