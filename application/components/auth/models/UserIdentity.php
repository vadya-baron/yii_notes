<?php

declare(strict_types=1);


namespace components\auth\models;

use components\auth\entities\User;
use components\auth\filters\UserFilter;
use components\auth\interfaces\IUserRepository;
use Yii;
use yii\web\IdentityInterface;

class UserIdentity implements IdentityInterface
{
    public int $id;
    public string $username;
    public string $email;
    public string $name;
    public static function findIdentity($id)
    {
        $userRepository = Yii::$container->get(IUserRepository::class);
        $user = $userRepository->getUserById(UserFilter::make(id: $id));
        if (!$user) {
            return null;
        }

        return self::getIdentityByUser(User::make(...$user));
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
    }

    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
    }

    public static function getIdentityByUser(User $user)
    {
        $identity = new self();
        $identity->id = $user->getId();
        $identity->username = $user->getUsername();
        $identity->email = $user->getEmail();
        $identity->name = $user->getName();
        return $identity;
    }
}