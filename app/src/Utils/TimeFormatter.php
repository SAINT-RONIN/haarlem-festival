<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * Utility for formatting timestamps as human-readable strings.
 *
 * Stateless utility - used by ViewModels and Services for display formatting.
 */
class TimeFormatter
{
    /**
     * Formats a timestamp as a human-readable "time ago" string.
     *
     * @param string|null $timestamp The UTC timestamp (Y-m-d H:i:s format)
     * @return string Human-readable time string (e.g., "2h ago", "yesterday")
     */
    public static function formatTimeAgo(?string $timestamp): string
    {
        if ($timestamp === null) {
            return 'recently';
        }

        $time = self::parseUtcTimestamp($timestamp);
        if ($time === null) {
            return 'recently';
        }

        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        return self::formatDiff($now->diff($time));
    }

    /**
     * Formats a DateInterval as a human-readable string.
     */
    private static function formatDiff(\DateInterval $diff): string
    {
        if ($diff->days === 0) {
            return self::formatSameDay($diff);
        }

        if ($diff->days === 1) {
            return 'yesterday';
        }

        if ($diff->days < 7) {
            return $diff->days . 'd ago';
        }

        if ($diff->days < 30) {
            $weeks = (int)floor($diff->days / 7);
            return $weeks . 'w ago';
        }

        $months = (int)floor($diff->days / 30);
        return $months . 'mo ago';
    }

    /**
     * Formats a same-day time difference.
     */
    private static function formatSameDay(\DateInterval $diff): string
    {
        if ($diff->h === 0) {
            return $diff->i . 'm ago';
        }
        return $diff->h . 'h ago';
    }

    private static function parseUtcTimestamp(string $timestamp): ?\DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s',
            $timestamp,
            new \DateTimeZone('UTC'),
        ) ?: null;
    }
}
