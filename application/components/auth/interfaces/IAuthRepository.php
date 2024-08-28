<?php

declare(strict_types=1);


namespace components\auth\interfaces;

use components\auth\entities\Auth;
use components\auth\filters\AuthFilter;

interface IAuthRepository
{
    public function getAuth(AuthFilter $filter): ?array;
    public function saveAuth(Auth &$entity): void;
}