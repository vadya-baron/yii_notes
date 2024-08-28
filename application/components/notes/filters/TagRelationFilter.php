<?php

declare(strict_types=1);


namespace components\notes\filters;

use commonComponents\filters\Filter;

class TagRelationFilter extends Filter
{
    /**
     * @param array<int>|null $tagIds
     * @param array<int>|null $noteIds
     */
    public function __construct(
        protected ?array $tagIds = null,
        protected ?array $noteIds = null,
    ) {
    }

    /**
     * @param array<int> $tagIds
     */
    public function addTagIds(array $tagIds): self
    {
        $this->tagIds = $tagIds;
        return $this;
    }

    /**
     * @param array<int> $noteIds
     */
    public function addNoteIds(array $noteIds): self
    {
        $this->noteIds = $noteIds;
        return $this;
    }

    public function getTagIds(): ?array
    {
        return $this->tagIds;
    }

    public function getNoteIds(): ?array
    {
        return $this->noteIds;
    }
}