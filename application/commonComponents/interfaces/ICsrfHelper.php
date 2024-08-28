<?php

declare(strict_types=1);


namespace commonComponents\interfaces;

interface ICsrfHelper
{
    public function getCsrfFieldName(): string;
    public function getCsrfFieldValue(): string;
}