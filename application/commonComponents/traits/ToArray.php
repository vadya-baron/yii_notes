<?php

declare(strict_types=1);


namespace commonComponents\traits;

trait ToArray
{
    public function toArray(): array
    {
        $data = [];
        foreach (get_object_vars($this) as $key => $value) {
            if ($value === null) {
                continue;
            }
            $data[$key] = $value;
        }

        return $data;
    }
}