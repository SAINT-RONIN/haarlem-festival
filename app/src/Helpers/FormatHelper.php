<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Centralized formatting utilities for consistent display across the application.
 */
final class FormatHelper
{
    public const CMS_DATE_FORMAT = 'd M Y, H:i';

    public static function fileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        return round($bytes / 1024, 1) . ' KB';
    }

    public static function price(float $amount, string $symbol = '€'): string
    {
        return $symbol . number_format($amount, 2, '.', '');
    }
}
