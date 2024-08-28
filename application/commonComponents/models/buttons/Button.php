<?php

declare(strict_types=1);


namespace commonComponents\models\buttons;

use commonComponents\traits\HtmlData;
use commonComponents\traits\Makeable;
use commonComponents\traits\StringHash;

class Button
{
    use Makeable;
    use StringHash;
    use HtmlData;

    public function __construct(
        protected string $type = 'submit',
        protected string $name = 'button',
        protected ?string $label = null,
        protected ?string $class = null,
        protected ?string $id = null,
        protected ?string $note = null,
        protected ?array $data = null,
    ) {
        if (!$id) {
            $this->id = $this->getHash(Button::class . $name . $type);
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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function getData(): ?string
    {
        return $this->getHtmlData($this->data);
    }
}