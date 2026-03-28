<?php

declare(strict_types=1);

namespace App\Infrastructure;

/**
 * Centralized path resolution for the application.
 *
 * Provides consistent path resolution for both Docker and local environments.
 */
class PathResolver
{
    /**
     * Gets the absolute path to the public directory.
     */
    public static function getPublicPath(): string
    {
        // Docker environment
        if (is_dir('/app/public')) {
            return '/app/public';
        }

        // Local development
        $realPath = realpath(__DIR__ . '/../../public');
        return $realPath !== false ? $realPath : __DIR__ . '/../../public';
    }

    /**
     * Gets the absolute path for uploads in a specific folder.
     *
     * @param string $folder Subfolder within Image directory (e.g., 'cms', 'jazz')
     */
    public static function getUploadPath(string $folder = 'cms'): string
    {
        return self::getPublicPath() . '/assets/Image/' . $folder;
    }

    /**
     * Gets the web-accessible relative path for a file in uploads.
     *
     * @param string $folder Subfolder within Image directory
     * @param string $fileName The file name
     */
    public static function getUploadRelativePath(string $folder, string $fileName): string
    {
        return '/assets/Image/' . $folder . '/' . $fileName;
    }

    /**
     * Gets the absolute path where generated ticket PDFs are stored.
     */
    public static function getTicketAssetPath(): string
    {
        return self::getPublicPath() . '/assets/tickets';
    }

    /**
     * Gets the web-accessible relative path for a generated ticket PDF.
     */
    public static function getTicketAssetRelativePath(string $fileName): string
    {
        return '/assets/tickets/' . $fileName;
    }
}
