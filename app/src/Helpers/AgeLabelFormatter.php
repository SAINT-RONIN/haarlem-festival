<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Centralizes age-restriction label formatting used on event cards, schedule entries,
 * and program items. Handles min-only ("Age 12+"), max-only ("Up to age 16"),
 * range ("Age 10-14"), and exact-age cases, with automatic normalization.
 */
final class AgeLabelFormatter
{
    /**
     * Produces a compact age label for display on event cards.
     * Returns null when no age restriction applies (both null or zero).
     */
    public static function format(?int $minAge, ?int $maxAge): ?string
    {
        [$minAge, $maxAge] = self::normalize($minAge, $maxAge);

        if ($minAge === null && $maxAge === null) {
            return null;
        }

        if ($minAge !== null && $maxAge !== null) {
            if ($minAge === $maxAge) {
                return 'Age ' . $minAge;
            }

            return 'Age ' . $minAge . '-' . $maxAge;
        }

        if ($minAge !== null) {
            return 'Age ' . $minAge . '+';
        }

        return 'Up to age ' . $maxAge;
    }

    /**
     * Produces a verbose age requirement sentence for detail pages and tooltips.
     * Always returns a string ("All ages" when no restriction applies).
     */
    public static function formatRequirement(?int $minAge, ?int $maxAge): string
    {
        [$minAge, $maxAge] = self::normalize($minAge, $maxAge);

        if ($minAge === null && $maxAge === null) {
            return 'All ages';
        }

        if ($minAge !== null && $maxAge !== null) {
            if ($minAge === $maxAge) {
                return 'Required age: ' . $minAge . ' years old';
            }

            return 'Age requirement: ' . $minAge . ' to ' . $maxAge . ' years old';
        }

        if ($minAge !== null) {
            return 'Minimum age requirement: ' . $minAge . ' years old';
        }

        return 'Maximum age: ' . $maxAge . ' years old';
    }

    /**
     * Appends an age label to an existing labels array if one doesn't already exist.
     * Prevents duplicate age labels when labels are assembled from multiple sources.
     *
     * @param array<int, string> $labels
     * @return array<int, string>
     */
    public static function appendToLabels(array $labels, ?int $minAge, ?int $maxAge): array
    {
        $ageLabel = self::format($minAge, $maxAge);
        if ($ageLabel === null || self::containsAgeLabel($labels)) {
            return $labels;
        }

        $labels[] = $ageLabel;

        return $labels;
    }

    /**
     * @param array<int, string> $labels
     */
    private static function containsAgeLabel(array $labels): bool
    {
        foreach ($labels as $label) {
            $normalized = strtolower(trim($label));
            if (str_starts_with($normalized, 'age ') || str_starts_with($normalized, 'up to age ')) {
                return true;
            }

            if ((bool) preg_match('/\b\d{1,2}\+/', $normalized)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Treats zero/negative ages as null and swaps min/max if they are inverted.
     *
     * @return array{0: ?int, 1: ?int}
     */
    private static function normalize(?int $minAge, ?int $maxAge): array
    {
        $minAge = self::normalizeSingleAge($minAge);
        $maxAge = self::normalizeSingleAge($maxAge);

        if ($minAge !== null && $maxAge !== null && $minAge > $maxAge) {
            return [$maxAge, $minAge];
        }

        return [$minAge, $maxAge];
    }

    private static function normalizeSingleAge(?int $value): ?int
    {
        if ($value === null || $value <= 0) {
            return null;
        }

        return $value;
    }
}
