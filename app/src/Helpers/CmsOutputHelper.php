<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Helper for CMS content output.
 *
 * Provides functions to safely output CMS content,
 * handling both plain text and HTML content appropriately.
 */
class CmsOutputHelper
{
    /**
     * Outputs plain text content, stripping any HTML tags.
     * Use for headings, titles, buttons, short labels.
     *
     * @param string|null $content The CMS content
     * @return string Escaped plain text
     */
    public static function text(?string $content): string
    {
        if ($content === null) {
            return '';
        }
        // Strip HTML tags and decode entities, then escape for output
        $plain = strip_tags(html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        return htmlspecialchars($plain, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Outputs HTML content with unsafe inline styles removed.
     * Use for descriptions and rich text areas.
     *
     * @param string|null $content The CMS HTML content
     * @return string The cleaned HTML content (not escaped)
     */
    public static function html(?string $content): string
    {
        if ($content === null) {
            return '';
        }

        $clean = (string)$content;

        // Remove any style attributes that contain text-decoration: underline
        $clean = preg_replace(
            '/\sstyle=("|\')[^"\']*text-decoration\s*:\s*underline[^"\']*("|\')/i',
            '',
            $clean
        );

        if ($clean === null) {
            return $content;
        }

        return $clean;
    }

    /**
     * Gets a string value from content array with default fallback.
     * Use for extracting values from CMS content arrays.
     *
     * @param array $content The content array
     * @param string $key The key to look up
     * @param string $default Default value if key not found or empty
     * @return string The value or default
     */
    public static function getString(array $content, string $key, string $default = ''): string
    {
        $value = $content[$key] ?? null;
        return is_string($value) && $value !== '' ? $value : $default;
    }
}
