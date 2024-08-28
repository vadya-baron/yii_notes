<?php

declare(strict_types=1);


namespace components\auth\repositories;

use commonComponents\repositories\DefaultRepository;
use components\auth\entities\Auth;
use components\auth\exceptions\AuthQueryException;
use components\auth\filters\AuthFilter;
use components\auth\interfaces\IAuthRepository;

class AuthRepository extends DefaultRepository implements IAuthRepository
{
    protected string $table = '{{%auth}}';
    protected array $tableFields = [
        'id',
        'user_id',
        'social_id',
        'client',
        'access_token',
        'refresh_token',
        'device_id',
        'expired_date_time',
    ];

    protected bool $timestamp = false;
    public function getAuth(AuthFilter $filter): ?array
    {
        $data = $this->getItems(filter: $filter);
        if (!$data) {
            return null;
        }

        $data = array_reverse($data);
        return array_pop($data);
    }

    public function saveAuth(Auth &$entity): void
    {
        $error = '';
        $id = $this->saveData(data: $entity->toArray(), id: $entity->getId(), error: $error);
        if (!$id) {
            throw new AuthQueryException($error);
        }
        $entity->addId($id);
    }
}