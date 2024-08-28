<?php

declare(strict_types=1);


namespace commonComponents\traits;

trait StringHash
{
    protected function getHash(string $data): string
    {
        return md5($data);
    }
}