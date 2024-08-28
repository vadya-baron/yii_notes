<?php

declare(strict_types=1);


namespace components\notes\entities;

use commonComponents\traits\Makeable;
use commonComponents\traits\ToArray;

class TagRelation
{
    use Makeable;
    use ToArray;

    public function __construct(
        protected int $tag_id,
        protected int $note_id,
    ) {
    }

    public function getTagId(): int
    {
        return $this->tag_id;
    }

    public function getNoteId(): int
    {
        return $this->note_id;
    }
}