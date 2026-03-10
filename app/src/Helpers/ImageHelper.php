<?php

declare(strict_types=1);

namespace App\Helpers;

class ImageHelper
{
    private const DEFAULT_IMAGE_PATH = '/assets/Image/Image (Story).png';
    private const VALID_IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'gif', 'heic'];

    public static function validatePath(string $path, string $fallback = self::DEFAULT_IMAGE_PATH): string
    {
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

        if (!str_starts_with($path, '/assets/')) {
            return $fallback;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($extension, self::VALID_IMAGE_EXTENSIONS, true)) {
            return $fallback;
        }

        return $path;
    }

    public static function altTextFromFilename(string $filename, string $suffix = ''): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $name = str_replace(['-', '_'], ' ', $name);
        $alt = ucfirst($name);

        return $suffix !== '' ? "{$alt} - {$suffix}" : $alt;
    }

    public static function getStringValue(array $content, string $key, string $default): string
    {
        $value = $content[$key] ?? null;

        return is_string($value) && $value !== '' ? $value : $default;
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
