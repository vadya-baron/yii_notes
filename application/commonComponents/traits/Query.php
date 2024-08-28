<?php

declare(strict_types=1);


namespace commonComponents\traits;

use Throwable;
use yii\db\Query as YiiQuery;

trait Query
{
    protected function saveItem(
        string $table,
        array $fields,
        array $data,
        ?int $id = null,
        ?string &$error = null,
    ): ?int {
        $attributes = $this->filterArrayByKeys(data: $data, keys: $fields);
        unset($attributes['id']);
        if (empty($attributes)) {
            $error = 'attributes not found';
            return null;
        }

        if ($id) {
            if (!$this->update(
                table: $table,
                id: $id,
                attributes: $attributes,
                error: $error,
            )) {
                return null;
            }
            return $id;
        } else {
            return $this->create(
                table: $table,
                attributes: $attributes,
                error: $error
            );
        }
    }

    protected function deleteItem(
        string $table,
        int $id,
        ?string &$error = null
    ): bool {
        try {
            $this->connection->createCommand()->delete(
                table: $table,
                condition: ['id' => $id]
            )->execute();
            return true;
        } catch (Throwable $e) {
            $error = $e->getMessage();
            return false;
        }
    }

    protected function getItem(
        int $id,
        mixed $filter = null,
        ?array $fields = null,
    ): ?array {
        $query = $this->getComplexQuery(filter: $filter, fields: $fields);
        $query->andWhere(['id' => $id]);
        $data = $query->one();
        if (!is_array($data)) {
            return null;
        }

        return $data;
    }

    protected function getItems(
        mixed $filter = null,
        ?array $fields = null,
    ): array {
        return $this->getComplexQuery(filter: $filter, fields: $fields)
            ->all(db: $this->connection);
    }

    protected function getCount(mixed $filter = null): int
    {
        $count = $this->getComplexQuery(filter: $filter)->count();
        if (!is_int($count)) {
            return 0;
        }

        return $count;
    }

    protected function getComplexQuery(
        mixed $filter = null,
        ?array $fields = null,
    ): YiiQuery {
        $query = $this->getQuery($fields);

        if ($filter) {
            $query = $this->appendQueryFilter(query: $query, filter: $filter);
        }

        return $query;
    }

    protected function appendQueryFilter(
        YiiQuery $query,
        mixed $filter = null
    ): YiiQuery {
        return $query;
    }

    private function update(
        string $table,
        int $id,
        array $attributes,
        ?string &$error = null
    ): bool {
        $attributes['update_at'] = date('Y-m-d H:i:s');
        try {
            $this->connection->createCommand()->update(
                table: $table,
                columns: $attributes,
                condition: ['id' => $id]
            )->execute();
            return true;
        } catch (Throwable $e) {
            $error = $e->getMessage();
            return false;
        }
    }

    private function create(
        string $table,
        array $attributes,
        ?string &$error = null
    ): ?int {
        try {
            $this->connection->createCommand()->insert(
                table: $table,
                columns:  $attributes
            )->execute();
            $id = (int)$this->connection->getLastInsertID();
            if (!$id) {
                return null;
            }
        } catch (Throwable $e) {
            $error = $e->getMessage();
            return null;
        }

        return $id;
    }

    private function filterArrayByKeys(
        array $data,
        array $keys
    ): array {
        if (!$data) {
            return [];
        }

        return array_intersect_key(
            $data,
            array_flip($keys)
        );
    }
}