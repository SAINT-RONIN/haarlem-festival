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

    /**
     * Generates a URL slug from a human-readable title.
     * Lowercases, replaces non-alphanumeric characters with dashes, collapses doubles.
     */
    public static function generate(string $title): string
    {
        $slug = strtolower(trim($title));
        $slug = (string) preg_replace('/[^a-z0-9]+/', '-', $slug);
        return trim($slug, '-');
    }
}
