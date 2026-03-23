<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Centralized formatting utilities for consistent display across the application.
 */
final class FormatHelper
{
    /** Date format used in CMS list tables, e.g. "23 Mar 2026, 14:30". */
    public const CMS_DATE_FORMAT = 'd M Y, H:i';
    /** ISO-style short date for forms and data attributes. */
    public const SHORT_DATE = 'Y-m-d';
    /** 24-hour time without seconds. */
    public const TIME_SHORT = 'H:i';

    /**
     * Formats a byte count into a human-readable size string (KB or MB).
     * Use when displaying uploaded file sizes in the CMS media library.
     */
    public static function fileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        return round($bytes / 1024, 1) . ' KB';
    }

    /**
     * Formats a monetary amount with a currency symbol (default euro).
     * Always outputs two decimal places with no thousands separator.
     */
    public static function price(float $amount, string $symbol = '€'): string
    {
        return $symbol . number_format($amount, 2, '.', '');
    }

    /**
     * Converts a snake_case item key to a human-readable label.
     */
    public static function formatFieldLabel(string $itemKey): string
    {
        return ucwords(str_replace('_', ' ', $itemKey));
    }
}
