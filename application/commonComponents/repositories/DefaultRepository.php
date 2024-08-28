<?php

declare(strict_types=1);


namespace commonComponents\repositories;


use commonComponents\interfaces\IDefaultRepository;
use commonComponents\traits\Connection;
use commonComponents\traits\Query;
use yii\db\Query as YiiQuery;

class DefaultRepository implements IDefaultRepository
{
    use Connection;
    use Query;

    protected string $table;
    protected array $tableFields;
    protected bool $timestamp = false;

    public function getData(int $id, mixed $filter = null, ?array $fields = null): ?array
    {
        return $this->getItem(id: $id, filter: $filter, fields: $fields);
    }

    public function getList(mixed $filter = null, ?array $fields = null): array
    {
        return $this->getItems(filter: $filter, fields: $fields);
    }

    public function getItemsCount(mixed $filter = null): int
    {
        return $this->getCount(filter: $filter);
    }

    public function saveData(array $data, ?int $id = null, ?string &$error = null): ?int
    {
        if (!$data) {
            return null;
        }

        $id = $this->saveItem(
            table: $this->table,
            fields: $this->tableFields,
            data: $data,
            id: $id,
            error: $error
        );
        if (!is_int($id)) {
            return null;
        }

        return $id;
    }

    public function deleteData(int $id, ?string &$error = null): bool
    {
        return $this->deleteItem(
            table: $this->table,
            id: $id,
            error: $error
        );
    }

    protected function getQuery(?array $fields = null): YiiQuery
    {
        $query = new YiiQuery;

        if (!$fields) {
            $fields = ['*'];
        }

        if ($this->timestamp) {
            $orderBy = $this->table . '.create_at DESC, ' . $this->table . '.id DESC';
        } else {
            $orderBy = $this->table . '.id DESC';
        }

        $query
            ->select(columns: $fields)
            ->from(tables: $this->table)
            ->orderBy(columns: $orderBy);

        return $query;
    }
}