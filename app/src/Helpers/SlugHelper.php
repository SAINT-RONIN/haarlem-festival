<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Shared slug normalization logic for detail page services.
 */
final class SlugHelper
{
    /**
     * Normalizes a URL slug: decodes, lowercases, trims whitespace and dashes.
     * Returns null if the slug is empty or contains a path separator (invalid).
     */
    public static function normalize(string $slug): ?string
    {
        $normalized = trim(strtolower(rawurldecode($slug)));
        if ($normalized === '' || str_contains($normalized, '/')) {
            return null;
        }
        return trim($normalized, '-');
    }
}
