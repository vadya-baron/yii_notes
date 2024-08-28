<?php

declare(strict_types=1);


namespace commonComponents\traits;

/**
 * @template TKey of array-key
 */
trait ArrayFilter
{
    /**
     * @param callable|null $callback
     * @return array<TKey, mixed>
     */
    protected function filter(callable $callback = null): array
    {
        if ($callback && $this->items) {
            return array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH);
        }

        return $this->items;
    }
}