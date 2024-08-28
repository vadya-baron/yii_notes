<?php

declare(strict_types=1);


namespace components\notes\repositories;

use commonComponents\repositories\DefaultRepository;
use components\notes\entities\Tag;
use components\notes\entities\TagRelation;
use components\notes\exceptions\TagQueryException;
use components\notes\filters\TagFilter;
use components\notes\filters\TagRelationFilter;
use components\notes\interfaces\ITagRepository;
use Throwable;
use yii\db\Query as YiiQuery;

class TagRepository extends DefaultRepository implements ITagRepository
{
    protected string $table = '{{%tags}}';
    protected string $tableRelations = '{{%relations}}';
    protected array $tableFields = [
        'id',
        'title',
    ];
    protected array $tableRelationsFields = [
        'tag_id',
        'note_id',
    ];

    protected bool $timestamp = false;

    public function getTagData(TagFilter $filter): ?array
    {
        if (!$filter->getId()) {
            return null;
        }

        return $this->getItem(id: $filter->getId(), filter: $filter);
    }

    public function getTagsData(?TagFilter $filter = null): ?array
    {
        $data = $this->getItems(filter: $filter);
        if (!$data) {
            return null;
        }

        return $data;
    }

    public function getRelationsData(?TagRelationFilter $filter = null): ?array
    {
        $query = new YiiQuery;

        if ($filter) {
            if ($filter->getNoteIds()) {
                $query->andWhere([
                    'IN',
                    $this->tableRelations . '.note_id',
                    $filter->getNoteIds()
                ]);
            }

            if ($filter->getTagIds()) {
                $query->andWhere([
                    'IN',
                    $this->tableRelations . '.tag_id',
                    $filter->getTagIds()
                ]);
            }
        }

        $query->from(tables: $this->tableRelations);

        $data = $query->all();
        if (!$data) {
            return null;
        }

        return $data;
    }

    public function countTags(?TagFilter $filter = null): int
    {
        return $this->getCount(filter: $filter);
    }

    public function saveTag(Tag &$entity): void
    {
        $error = '';
        $id = $this->saveData(data: $entity->toArray(), id: $entity->getId(), error: $error);
        if (!$id) {
            throw new TagQueryException($error);
        }
        $entity->addId($id);
    }

    /**
     * @param array<TagRelation> $entities
     * @throws TagQueryException
     */
    public function addTagRelations(array $entities): void
    {
        if (!$entities) {
            return;
        }
        $rows = [];

        foreach ($entities as $entity) {
            $rows[] = [
                'tag_id' => $entity->getTagId(),
                'note_id' => $entity->getNoteId(),
            ];
        }

        try {
            $this->connection->createCommand()->batchInsert(
                table: $this->tableRelations,
                columns:  ['tag_id', 'note_id'],
                rows:  $rows,
            )->execute();
        } catch (Throwable $e) {
            throw new TagQueryException($e->getMessage());
        }
    }

    /**
     * @param array<TagRelation> $entities
     * @throws TagQueryException
     */
    public function deleteTagRelations(array $entities): void
    {
        $tagIds = [];
        $noteIds = [];

        foreach ($entities as $entity) {
            $tagIds[] = $entity->getTagId();
            $noteIds[] = $entity->getNoteId();
        }

        try {
            $this->connection->createCommand()->delete(
                table: $this->tableRelations,
                condition:  [
                    $this->tableRelations . '.tag_id' => $tagIds,
                    $this->tableRelations . '.note_id' => $noteIds,
                ],
            )->execute();
        } catch (Throwable $e) {
            throw new TagQueryException($e->getMessage());
        }
    }

    /**
     * @throws TagQueryException
     */
    public function deleteTag(int $id): void
    {
        $error = '';
        if (!$this->deleteItem(table: $this->table, id: $id, error: $error)) {
            throw new TagQueryException($error);
        }
    }

    /**
     * @param YiiQuery $query
     * @param TagFilter|null $filter
     * @return YiiQuery
     */
    protected function appendQueryFilter(
        YiiQuery $query,
        mixed $filter = null
    ): YiiQuery {
        if (!$filter) {
            return $query;
        }

        if ($filter->getIds()) {
            $query->andWhere(['IN', 'id', $filter->getIds()]);
        }

        if ($filter->getId()) {
            $query->andWhere(['id' => $filter->getId()]);
        }

        if ($filter->getTitle()) {
            $query->andWhere(['title' => $filter->getTitle()]);
        }

        return $query;
    }
}