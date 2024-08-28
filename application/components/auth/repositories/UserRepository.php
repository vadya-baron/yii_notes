<?php

declare(strict_types=1);


namespace components\auth\repositories;

use commonComponents\repositories\DefaultRepository;
use components\auth\entities\User;
use components\auth\exceptions\UserQueryException;
use components\auth\filters\UserFilter;
use components\auth\interfaces\IUserRepository;
use yii\db\Query as YiiQuery;

class UserRepository  extends DefaultRepository implements IUserRepository
{
    protected string $table = '{{%users}}';
    protected array $tableFields = [
        'id',
        'username',
        'auth_key',
        'password_hash',
        'password_reset_token',
        'email',
        'name',
        'status',
        'create_at',
        'update_at',
    ];

    protected bool $timestamp = true;

    public function getUserByEmail(UserFilter $filter): ?array
    {
        if (!$filter->getEmail()) {
            return null;
        }

        $data = $this->getItems(filter: $filter);
        if (!$data) {
            return null;
        }

        $data = array_reverse($data);
        return array_pop($data);
    }

    public function getUserById(UserFilter $filter): ?array
    {
        if (!$filter->getId()) {
            return null;
        }

        $data = $this->getItem(id: $filter->getId(), filter: $filter);
        if (!$data) {
            return null;
        }

        return $data;
    }

    public function saveUser(User &$entity): void
    {
        $error = '';
        $id = $this->saveData(data: $entity->toArray(), id: $entity->getId(), error: $error);
        if (!$id) {
            throw new UserQueryException($error);
        }
        $entity->addId($id);
    }

    /**
     * @param YiiQuery $query
     * @param UserFilter|null $filter
     * @return YiiQuery
     */
    protected function appendQueryFilter(
        YiiQuery $query,
        mixed $filter = null
    ): YiiQuery {
        if (!$filter) {
            return $query;
        }

        if ($filter->getId()) {
            $query->andWhere([$this->table . '.id' => $filter->getId()]);
        }

        if ($filter->getUsername()) {
            $query->andWhere([$this->table . '.username' => $filter->getUsername()]);
        }

        if ($filter->getEmail()) {
            $query->andWhere([$this->table . '.email' => $filter->getEmail()]);
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
            case 'username':
                $query->orderBy($this->table . '.username ASC, id ASC');
                break;
        }

        return $query;
    }
}