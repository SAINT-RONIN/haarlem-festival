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
     * Outputs HTML content as-is (for TinyMCE rich text).
     * Use for descriptions and rich text areas.
     *
     * @param string|null $content The CMS HTML content
     * @return string The HTML content (not escaped)
     */
    public static function html(?string $content): string
    {
        if ($content === null) {
            return '';
        }
        return $content;
    }
}

