<?php

declare(strict_types=1);


namespace components\notes\validators;

use components\notes\interfaces\INoteValidator;

class NoteValidator implements INoteValidator
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

        if (!$input['description'] ?? null) {
            $errors['description'] = 'Заполните описание';
        }

        if ($errors) {
            return false;
        }

        return true;
    }
}