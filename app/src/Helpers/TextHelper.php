<?php

declare(strict_types=1);

namespace App\Helpers;

final class TextHelper
{
    public static function firstNonEmpty(string ...$values): string
    {
        foreach ($values as $value) {
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    public static function stripHtmlToText(string $value): string
    {
        return trim(strip_tags(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    }

    private function __construct()
    {
    }
}
