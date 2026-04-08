<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Provides cache-busting version strings for static front-end assets
 * by reading the file's last-modified timestamp.
 */
final class AssetVersionHelper
{
    /** Returns the file's last-modified timestamp as a cache-busting version string. */
    public static function resolveJsVersion(string $filePath): string
    {
        if (!file_exists($filePath)) {
            return '';
        }

        return (string) filemtime($filePath);
    }
}
