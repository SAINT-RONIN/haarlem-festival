<?php

declare(strict_types=1);

namespace App\Helpers;

{
    private const DEFAULT_IMAGE_PATH = '/assets/Image/Image (Story).png';

    public static function validatePath(string $path, string $fallback = self::DEFAULT_IMAGE_PATH): string
    {
        if ($path === '') {
            return $fallback;
        }

        return $path;
    }

    {
        $name = pathinfo($filename, PATHINFO_FILENAME);

    }

    }
}
