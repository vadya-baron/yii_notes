<?php

declare(strict_types=1);


namespace commonComponents\helpers;

use commonComponents\interfaces\ICsrfHelper;
use Yii;

class YiiCsrfHelper implements ICsrfHelper
{

    public function getCsrfFieldName(): string
    {
        return Yii::$app->request->csrfParam;
    }

    public function getCsrfFieldValue(): string
    {
        return Yii::$app->request->csrfToken;
    }
}