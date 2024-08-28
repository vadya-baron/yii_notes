<?php

declare(strict_types=1);


namespace components\notes\models;

use commonComponents\traits\Makeable;
use commonComponents\traits\ToArray;

class Tag
{
    use Makeable;
    use ToArray;

    public function __construct(
        protected int $id,
        protected string $title,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}