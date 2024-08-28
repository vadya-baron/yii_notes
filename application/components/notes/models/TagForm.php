<?php

declare(strict_types=1);


namespace components\notes\models;

use commonComponents\models\forms\Form;
use commonComponents\traits\Makeable;

class TagForm
{
    use Makeable;

    public function __construct(
        protected string $title,
        protected string $description,
        protected Form $form,
        protected ?array $messages = null,
        protected ?array $errors = null,
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function getMessages(): ?array
    {
        return $this->messages;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }
}