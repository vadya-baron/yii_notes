<?php

declare(strict_types=1);


namespace commonComponents\traits;

trait Makeable
{
    public static function make(...$attributes): static
    {
        return new static(...$attributes);
    }
}