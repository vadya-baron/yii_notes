<?php

declare(strict_types=1);


namespace commonComponents\traits;

trait HtmlData
{
    /**
     * @param array<string, string>|null $data
     */
    protected function getHtmlData(?array $data = null): ?string
    {
        if (!$data) {
            return null;
        }
        $result = '';
        foreach ($data as $key => $value) {
            $result = $result . ' ' . $key . '="' . $value . '"';
        }

        return $result;
    }
}