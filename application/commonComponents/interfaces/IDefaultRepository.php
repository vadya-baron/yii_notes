<?php

declare(strict_types=1);


namespace commonComponents\interfaces;

use commonComponents\filters\Filter;

interface IDefaultRepository
{
    public function getData(int $id, ?Filter $filter = null, ?array $fields = null): ?array;
    public function getList(?Filter $filter = null, ?array $fields = null): array;
    public function getItemsCount(?Filter $filter = null): int;
    public function saveData(array $data, ?int $id = null, ?string &$error = null): ?int;
    public function deleteData(int $id, ?string &$error = null): bool;
}