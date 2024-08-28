<?php

declare(strict_types=1);


namespace components\auth\interfaces;

use components\auth\entities\User;
use components\auth\filters\UserFilter;

interface IUserRepository
{
    public function getUserByEmail(UserFilter $filter): ?array;
    public function getUserById(UserFilter $filter): ?array;
    public function saveUser(User &$entity): void;
}