<?php

declare(strict_types=1);

namespace components\auth;


use components\auth\entities\User as UserEntity;
use components\auth\exceptions\AuthHandlerException;
use components\auth\filters\AuthFilter;
use components\auth\filters\UserFilter;
use components\auth\handlers\OauthHandler;
use components\auth\interfaces\IAuth;
use components\auth\interfaces\IAuthRepository;
use components\auth\interfaces\IUserRepository;
use components\auth\models\Auth as AuthModel;
use components\auth\entities\Auth as AuthEntity;
use components\auth\models\PKCEHash;
use components\auth\models\UserIdentity;
use Throwable;

class Auth implements IAuth
{
    protected AuthModel $authModel;
    protected UserIdentity $identity;
    protected ?OauthHandler $handler = null;
    protected ?PKCEHash $hash = null;
    protected ?UserEntity $localUser = null;
    protected ?AuthEntity $localAuth = null;

    public function __construct(
        protected IUserRepository $userRepository,
        protected IAuthRepository $authRepository,
    ) {
    }

    /**
     * @throws AuthHandlerException
     */
    public function initAuth(
        string $state,
        array $params,
        ?array $query = [],
        ?array &$errors = [],
    ): void {
        $params['state'] = $state;
        try {
            $this->authModel = AuthModel::make(
                params: $params,
                query: $query,
            );
        } catch (Throwable $e){
            $errors[] = 'Не найден клиент авторизации';
            /** TODO логирование ошибки @param $e */
            return;
        }
        $this->buildHandler();
    }

    public function getState(): string
    {
        return $this->authModel->getState();
    }

    public function getAuthUrl(): string
    {
        return $this->handler->geAuthUrl(hash: $this->hash);
    }

    public function getClient(): ?string
    {
        return $this->authModel->getClient();
    }

    public function isEnablePKCE(): bool
    {
        return $this->authModel->getClientParams()['enablePKCE'] ?? false;
    }

    public function getAuthCodeVerifier(
        string $data,
        string $algo = 'sha256',
    ): string {
        if ($this->hash) {
            return $this->hash->getAuthCodeVerifier();
        }

        $this->hash = PKCEHash::make(data: $data);

        return $this->hash->getAuthCodeVerifier();
    }

    public function verify(
        array $session = [],
        ?array &$errors = [],
    ): bool {
        $testState = $session['state'] ?? null;
        if ($testState !== $this->getState()) {
            $errors[] = 'Недопустимый параметр состояния аутентификации.';
            return false;
        }

        if ($this->isEnablePKCE() && !($session['codeVerifier'] ?? null)) {
            return false;
        }

        return true;
    }

    public function auth(
        ?string $codeVerifier = null,
        ?array &$errors = [],
    ): void {
        $authData =  $this->handler->getAuthData(
            data: [
                'code' => $this->authModel->getAuthCode(),
                'client_id' => $this->authModel->getClientParams()['clientId'] ?? null,
                'grant_type' => 'authorization_code',
                'device_id' => $this->authModel->getDeviceId(),
                'state' => $this->authModel->getState(),
                'redirect_uri' => $this->authModel->getClientParams()['redirectUri'] ?? null,
                'code_verifier' => $codeVerifier,
                'scope' => $this->authModel->getClientParams()['scope'] ?? null,
            ],
            errors: $errors,
        );

        if ($errors) {
            return;
        }

        $this->authModel->addAuthData(authData: $authData);

        $this->setLocalUser($errors);
        if ($errors) {
            return;
        }
        $this->setLocalAuth($errors);
    }

    public function getIdentity(?array &$errors = []): ?UserIdentity
    {
        $authUser = $this->authModel->buildUser();
        $userData = $this->userRepository->getUserByEmail(
            filter: UserFilter::make(email: $authUser->getEmail())
        );
        if (!$userData) {
            $errors[] = 'Пользователь не найден';
            return null;
        }

        $user = UserEntity::make(...$userData);

        try {
            $this->identity = UserIdentity::getIdentityByUser($user);
        } catch (Throwable $e) {
            $errors[] = 'Ошибка входа';
            /** TODO логирование ошибки @param $e */
            return null;
        }

        return $this->identity;
    }

    public function getAuthorizationData(): ?array
    {
        return $this->authModel->getAuthorizationData();
    }

    /**
     * @throws AuthHandlerException
     */
    private function buildHandler(): void
    {
        $clientParams = $this->authModel->getClientParams();
        $class = $clientParams['handler'] ?? null;
        if (!$class) {
            throw new AuthHandlerException();
        }
        try {
            /** @var OauthHandler $handler */
            $this->handler = new $class();
            $this->handler->configure(query: $this->authModel->getQuery(), params: $clientParams);
        } catch (Throwable $e) {
            throw new AuthHandlerException($e->getMessage());
        }
    }

    private function setLocalUser(?array &$errors = []): void
    {
        if ($this->localUser) {
            return;
        }

        try {
            $user = $this->authModel->buildUser();
            $userData = $this->userRepository->getUserByEmail(
                filter: UserFilter::make(email: $user->getEmail())
            );
            if ($userData) {
                $this->localUser = $this->authModel->buildUser(userData: $userData);
                return;
            }
            $this->userRepository->saveUser(entity: $user);
            $this->localUser = $user;
        } catch (Throwable $e){
            $errors[] = 'Ошибка сохранения пользователя';
            /** TODO логирование ошибки @param $e */
            return;
        }
    }

    private function setLocalAuth(?array &$errors = []): void
    {
        $authData = $this->authRepository->getAuth(
            filter: AuthFilter::make(
                userId: $this->localUser->getId(),
                socialId: $this->authModel->getSocialId(),
                client: $this->authModel->getClient(),
            )
        );

        try {
            if ($authData) {
                $this->localAuth = $this->authModel->buildAuth(
                    userId: $this->localUser->getId(),
                    authData: $authData
                );
//                if ($this->localAuth->isExpired()) {
//                    $newAuthData = $this->handler->getRefreshData(
//                        data: [
//                            'grant_type' => 'refresh_token',
//                            'refresh_token' => $this->localAuth->getRefreshToken(),
//                            'client_id' => $this->authModel->getClientParams()['clientId'],
//                            'device_id' => $this->localAuth->getDeviceId(),
//                            'scope' => $this->authModel->getClientParams()['scope'] ?? null,
//                        ],
//                        errors: $errors,
//                    );
//
//                    if ($errors) {
//                        return;
//                    }
//
//                    $newAuthData['user_id'] = $this->localAuth->getUserId();
//                    $newAuthData['social_id'] = $this->localAuth->getSocialId();
//                    $newAuthData['client'] = $this->localAuth->getClient();
//                    $newAuthData['expired_date_time'] = $this->authModel->getExpiredDateTime(
//                        expiresIn: $newAuthData['expires_in'] ?? null
//                    );
//
//                    $this->localAuth = $this->authModel->buildAuth(
//                        userId: $this->localUser->getId(),
//                        authData: $newAuthData,
//                    );
//                    $this->authRepository->saveAuth(entity: $this->localAuth);
//                }

                return;
            }

            $this->localAuth = $this->authModel->buildAuth(
                userId: $this->localUser->getId()
            );

            $this->authRepository->saveAuth(entity: $this->localAuth);
        } catch (Throwable $e){
            $errors[] = 'Ошибка сохранения авторизации';
            /** TODO логирование ошибки @param $e */
            return;
        }
    }
}