<?php

declare(strict_types=1);

namespace commonComponents\models\forms;

use commonComponents\models\fields\Field;
use commonComponents\traits\Makeable;

class Form
{
    use Makeable;

    /**
     * @param array<Field> $fields
     * @param array $buttons
     */
    public function __construct(
        protected string $method,
        protected string $action,
        protected array $fields,
        protected array $buttons,
        protected ?array $options = null,
        protected ?array $errors = null,
        protected ?array $messages = null,
        protected ?string $header = null,
    ) {
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getButtons(): array
    {
        return $this->buttons;
    }

    public function getOptions(): ?array
    {
        return $this->options;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function getMessages(): ?array
    {
        return $this->messages;
    }

    public function getHeader(): ?string
    {
        return $this->header;
    }
}