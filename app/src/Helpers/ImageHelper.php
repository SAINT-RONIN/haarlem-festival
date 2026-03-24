<?php

declare(strict_types=1);

namespace App\Helpers;

final class ImageHelper
{
    private const DEFAULT_IMAGE_PATH = '/assets/Image/Image (Story).png';

    public static function validatePath(string $path, string $fallback = self::DEFAULT_IMAGE_PATH): string
    {
        if ($path === '') {
            return $fallback;
        }

        return $path;
    }

    public static function getStringValue(array $content, string $key, string $default): string
    {
        $value = $content[$key] ?? null;
        return is_string($value) && $value !== '' ? $value : $default;
    }

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
