<?php

declare(strict_types=1);


namespace commonComponents\models\fields;

use commonComponents\traits\HtmlData;
use commonComponents\traits\Makeable;
use commonComponents\traits\StringHash;

class Field
{
    use Makeable;
    use StringHash;
    use HtmlData;

    public function __construct(
        protected string $type,
        protected string $name,
        protected bool $required = false,
        protected ?string $value = null,
        protected ?string $label = null,
        protected ?string $placeholder = null,
        protected ?string $class = null,
        protected ?string $id = null,
        protected ?string $error = null,
        protected ?string $note = null,
        protected ?array $data = null,
        protected ?array $options = null,
        protected bool $active = true,
        protected bool $check = false,
    ) {
        if (!$id) {
            $this->id = $this->getHash(Field::class . $name . $type);
        }
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function getOptions(): ?array
    {
        return $this->options;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function isCheck(): bool
    {
        return $this->check;
    }

    public function getData(): ?string
    {
        return $this->getHtmlData($this->data);
    }
}