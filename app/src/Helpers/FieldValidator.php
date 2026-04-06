<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Shared field-level validation helpers for CMS forms.
 *
 * Used by CmsArtistsService, CmsRestaurantsService, and other admin services
 * that validate required string fields before persisting data.
 */
final class FieldValidator
{
    /**
     * Adds an error if the given value is empty.
     *
     * @param array<string, string> $errors Accumulator passed by reference
     */
    public static function requireNonEmpty(string $field, string $value, string $label, array &$errors): void
    {
        if ($value === '') {
            $errors[$field] = $label . ' is required.';
        }
    }
}
