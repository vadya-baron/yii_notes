<?php

declare(strict_types=1);


namespace components\notes\interfaces;

interface INoteValidator
{
    public function validate(array $input, array &$errors = []): bool;
}