<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Centralizes image-path validation and alt-text generation used by mappers
 * that convert CMS or event data into display-ready image URLs.
 */
final class ImageHelper
{
    private const DEFAULT_IMAGE_PATH = '/assets/Image/Image (Story).png';

    /**
     * Returns the given path if non-empty, otherwise a default placeholder image.
     * Use to guarantee every <img> tag receives a renderable src.
     */
    public static function validatePath(string $path, string $fallback = self::DEFAULT_IMAGE_PATH): string
    {
        if ($path === '') {
            return $fallback;
        }

        return $path;
    }

    /**
     * Reads a non-empty string from a keyed array, returning a default when missing.
     * Useful for pulling image paths or alt text from CMS content arrays.
     */
    public static function getStringValue(array $content, string $key, string $default): string
    {
        $value = $content[$key] ?? null;
        return is_string($value) && $value !== '' ? $value : $default;
    }

    /**
     * Derives human-readable alt text from a filename by stripping the extension
     * and converting hyphens/underscores to spaces (e.g. "jazz_hero-bg.png" becomes "Jazz Hero Bg").
     */
    public static function altTextFromFilename(string $filename, string $fallback = 'Image'): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        if ($name === '') {
            return $fallback;
        }

        $normalized = preg_replace('/[_\-]+/', ' ', $name);
        $normalized = preg_replace('/\s+/', ' ', (string)$normalized);
        $normalized = trim((string)$normalized);

        if ($normalized === '') {
            return $fallback;
        }

        return ucwords($normalized);
    }
}
