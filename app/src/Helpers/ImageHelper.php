<?php

declare(strict_types=1);

namespace App\Helpers;

final class ImageHelper
{
    private const DEFAULT_IMAGE_PATH = '/assets/Image/Image (Story).png';

    public static function getStringValue(array $content, string $key, string $default = ''): string
    {
        $value = $content[$key] ?? null;

        if (!is_string($value)) {
            return $default;
        }

        $value = trim($value);
        return $value !== '' ? $value : $default;
    }

    public static function validatePath(string $path, string $fallback = self::DEFAULT_IMAGE_PATH): string
    {
        $path = trim($path);
        if ($path === '') {
            return $fallback;
        }

        // Allow fully-qualified URLs used by embeds/CDNs.
        if (preg_match('#^https?://#i', $path) === 1) {
            return $path;
        }

        // Reject dangerous pseudo-protocols.
        if (preg_match('#^(javascript|data):#i', $path) === 1) {
            return $fallback;
        }

        return str_starts_with($path, '/') ? $path : '/' . ltrim($path, '/');
    }

    public static function altTextFromFilename(string $filename, string $fallback = 'Image'): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $name = trim(str_replace(['-', '_'], ' ', $name));
        $name = preg_replace('/\s+/', ' ', $name) ?? '';

        if ($name === '') {
            return $fallback;
        }

        return ucwords($name);
    }
}
