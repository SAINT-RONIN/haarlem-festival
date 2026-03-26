<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Helper for CMS content output.
 *
 * Provides functions to safely output CMS content,
 * handling both plain text and HTML content appropriately.
 */
final class CmsOutputHelper
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
     * Strips problematic inline style attributes from HTML content.
     * Use when cleaning raw CMS HTML for display in an editor or form input.
     *
     * @param string $rawHtml The raw HTML string to clean
     * @return string The cleaned HTML with underline styles removed
     */
    public static function cleanHtmlStyles(string $rawHtml): string
    {
        $cleaned = preg_replace(
            '/\sstyle=("|\')[^"\']*text-decoration\s*:\s*underline[^"\']*("|\')/i',
            '',
            $rawHtml
        );

        return $cleaned ?? $rawHtml;
    }

    /**
     * Normalizes a raw file path for safe display in an image preview.
     * Encodes each path segment individually and preserves the query string.
     *
     * @param string $rawFilePath The raw file path (may include query string)
     * @return string The normalized, URL-encoded path ready for src attribute
     */
    public static function normalizeImagePath(string $rawFilePath): string
    {
        if ($rawFilePath === '') {
            return '';
        }

        $path = parse_url($rawFilePath, PHP_URL_PATH);
        $query = parse_url($rawFilePath, PHP_URL_QUERY);

        if (!is_string($path) || $path === '') {
            return '';
        }

        $segments = array_map('rawurlencode', explode('/', ltrim($path, '/')));
        $normalized = '/' . implode('/', $segments);

        if (is_string($query) && $query !== '') {
            $normalized .= '?' . $query;
        }

        return $normalized;
    }
}
