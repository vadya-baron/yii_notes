<?php

declare(strict_types=1);


namespace components\notes\repositories;

use commonComponents\repositories\DefaultRepository;
use components\notes\Entities\Note;
use components\notes\exceptions\NoteQueryException;
use components\notes\filters\NoteFilter;
use components\notes\interfaces\INoteRepository;
use yii\db\Query as YiiQuery;

class NoteRepository extends DefaultRepository implements INoteRepository
{
    protected string $table = '{{%notes}}';
    protected array $tableFields = [
        'id',
        'user_id',
        'title',
        'description',
        'create_at',
        'update_at',
    ];

    protected bool $timestamp = true;
    public function getNoteData(NoteFilter $filter): ?array
    {
        if (!$filter->getId()) {
            return null;
        }

        return $this->getItem(id: $filter->getId(), filter: $filter);
    }

    public function getNotesData(NoteFilter $filter): ?array
    {
        $data = $this->getItems(filter: $filter);
        if (!$data) {
            return null;
        }

        return $data;
    }

    public function countNotes(NoteFilter $filter): int
    {
        return $this->getCount(filter: $filter);
    }

    /**
     * @throws NoteQueryException
     */
    public function saveNote(Note &$entity): void
    {
        $error = '';
        $id = $this->saveData(data: $entity->toArray(), id: $entity->getId(), error: $error);
        if (!$id) {
            throw new NoteQueryException($error);
        }
        $entity->addId($id);
    }

    /**
     * @throws NoteQueryException
     */
    public function deleteNote(int $id): void
    {
        $error = '';
        if (!$this->deleteData(id: $id, error: $error)) {
            throw new NoteQueryException($error);
        }
    }

    /**
     * @param YiiQuery $query
     * @param NoteFilter|null $filter
     * @return YiiQuery
     */
    protected function appendQueryFilter(
        YiiQuery $query,
        mixed $filter = null
    ): YiiQuery {
        if (!$filter) {
            return $query;
        }

        $query->andWhere([$this->table . '.user_id' => $filter->getUserId()]);

        if ($filter->getIds()) {
            $query->andWhere(['IN', $this->table . '.id', $filter->getIds()]);
        }

        if ($filter->getId()) {
            $query->andWhere([$this->table . '.id' => $filter->getId()]);
        }

        if ($filter->getSearch()) {
            $query->andWhere(['LIKE', $this->table . '.title', $filter->getSearch()]);
        }

        if ($filter->getLimit()) {
            $query->limit($filter->getLimit());
        }

        if ($filter->getOffset()) {
            $query->offset($filter->getOffset());
        }

        switch ($filter->getSort()) {
            case 'asc':
                $query->orderBy($this->table . '.create_at ASC, id ASC');
                break;
            case 'title':
                $query->orderBy($this->table . '.title ASC, id ASC');
                break;
        }

        return $query;
    }
}