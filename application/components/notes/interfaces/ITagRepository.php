<?php

declare(strict_types=1);


namespace components\notes\interfaces;

use components\notes\entities\TagRelation;
use components\notes\exceptions\TagQueryException;
use components\notes\filters\TagFilter;
use components\notes\entities\Tag;
use components\notes\filters\TagRelationFilter;

interface ITagRepository
{
    public function getTagData(TagFilter $filter): ?array;

    public function getTagsData(?TagFilter $filter = null): ?array;
    public function getRelationsData(?TagRelationFilter $filter = null): ?array;

    public function countTags(?TagFilter $filter = null): int;

    /**
     * @throws TagQueryException
     */
    public function saveTag(Tag &$entity): void;

    /**
     * @param array<TagRelation> $entities
     * @throws TagQueryException
     */
    public function addTagRelations(array $entities): void;

    /**
     * @param array<TagRelation> $entities
     * @throws TagQueryException
     */
    public function deleteTagRelations(array $entities): void;

    /**
     * @throws TagQueryException
     */
    public function deleteTag(int $id): void;
}