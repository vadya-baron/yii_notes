<?php

declare(strict_types=1);


namespace components\notes\validators;

use components\notes\interfaces\ITagValidator;

class TagValidator implements ITagValidator
{

    public function validate(array $input, array &$errors = []): bool
    {
        $title = $input['title'] ?? '';
        if (!$title) {
            $errors['title'] = 'Заполните заголовок';
        }

        if (mb_strlen($title) > 255) {
            $errors['title'] = 'Заголовок не должен превышать 255 символов';
        }

        if ($errors) {
            return false;
        }

        return true;
    }
}