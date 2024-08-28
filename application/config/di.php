<?php

Yii::$container->set(
    'components\auth\interfaces\IAuth',
    'components\auth\Auth'
);

Yii::$container->setSingleton('components\notes\interfaces\INoteRepository', function () {
    $repo = new components\notes\repositories\NoteRepository();
    $repo->setConnection(Yii::$app->db);
    return $repo;
});

Yii::$container->setSingleton('components\notes\interfaces\ITagRepository', function () {
    $repo = new components\notes\repositories\TagRepository();
    $repo->setConnection(Yii::$app->db);
    return $repo;
});

Yii::$container->setSingleton('components\auth\interfaces\IUserRepository', function () {
    $repo = new components\auth\repositories\UserRepository();
    $repo->setConnection(Yii::$app->db);
    return $repo;
});

Yii::$container->setSingleton('components\auth\interfaces\IAuthRepository', function () {
    $repo = new components\auth\repositories\AuthRepository();
    $repo->setConnection(Yii::$app->db);
    return $repo;
});

Yii::$container->set(
    'components\notes\interfaces\INotes',
    'components\notes\Notes'
);

Yii::$container->set(
    'components\notes\interfaces\IModelBuilder',
    'components\notes\modelBuilders\ModelBuilder'
);

Yii::$container->set(
    'commonComponents\interfaces\ICsrfHelper',
    'commonComponents\helpers\YiiCsrfHelper'
);

Yii::$container->set(
    'components\notes\interfaces\INoteValidator',
    'components\notes\validators\NoteValidator'
);

Yii::$container->set(
    'components\notes\interfaces\ITagValidator',
    'components\notes\validators\TagValidator'
);

Yii::$container->set(
    'components\notes\interfaces\ITagValidator',
    'components\notes\validators\TagValidator'
);
