<?php

declare(strict_types=1);

namespace App\Helpers;

use App\DTOs\Schedule\SessionWithEvent;
use App\Enums\PriceTierId;
use App\Models\EventSessionLabel;
use App\Models\EventSessionPrice;

/**
 * Shared History-specific helpers used by schedule and program flows.
 */
final class HistorySessionHelper
{
    /**
     * @param array<string, EventSessionPrice> $sharedPricesByKey
     * @param EventSessionPrice[] $prices
     * @return array<string, EventSessionPrice>
     */
    public static function mergeHighestPricesByKey(array $sharedPricesByKey, array $prices): array
    {
        foreach ($prices as $price) {
            $priceKey = self::resolvePriceKey($price);

            if (!isset($sharedPricesByKey[$priceKey])) {
                $sharedPricesByKey[$priceKey] = $price;
                continue;
            }

            if ((float)$price->price > (float)$sharedPricesByKey[$priceKey]->price) {
                $sharedPricesByKey[$priceKey] = $price;
            }
        }

        return $sharedPricesByKey;
    }

    public static function resolveSeatsAvailable(SessionWithEvent $session): int
    {
        if ($session->seatsAvailable !== null) {
            return max(0, $session->seatsAvailable);
        }

        $available = $session->capacityTotal - $session->soldSingleTickets - $session->soldReservedSeats;

        return max(0, $available);
    }

    /**
     * @param EventSessionLabel[] $labels
     */
    public static function resolveLanguageLabel(?string $languageCode, array $labels): ?string
    {
        foreach ($labels as $label) {
            $normalized = self::normalizeLanguageLabel($label->labelText);
            if ($normalized !== null) {
                return $normalized;
            }
        }

        return self::mapLanguageCodeToLabel($languageCode);
    }

    /**
     * @param EventSessionLabel[] $labels
     */
    public static function resolveLanguageKey(?string $languageCode, array $labels): ?string
    {
        $codeKey = self::normalizeLanguageCode($languageCode);
        if ($codeKey !== null) {
            return $codeKey;
        }

        foreach ($labels as $label) {
            $labelKey = self::normalizeLanguageKeyFromText($label->labelText);
            if ($labelKey !== null) {
                return $labelKey;
            }
        }

        return null;
    }

    private static function normalizeLanguageLabel(string $labelText): ?string
    {
        $trimmed = self::trimLanguagePrefix($labelText);
        if ($trimmed === null) {
            return null;
        }

        return self::mapLanguageCodeToLabel($trimmed) ?? $trimmed;
    }

    private static function normalizeLanguageKeyFromText(string $labelText): ?string
    {
        $trimmed = self::trimLanguagePrefix($labelText);
        if ($trimmed === null) {
            return null;
        }

        return self::normalizeLanguageCode($trimmed) ?? strtoupper($trimmed);
    }

    private static function trimLanguagePrefix(string $labelText): ?string
    {
        $trimmed = trim($labelText);
        if ($trimmed === '') {
            return null;
        }

        if (stripos($trimmed, 'In ') === 0) {
            $trimmed = trim(substr($trimmed, 3));
        }

        return $trimmed === '' ? null : $trimmed;
    }

    private static function mapLanguageCodeToLabel(?string $languageCode): ?string
    {
        return match (self::normalizeLanguageCode($languageCode)) {
            'ENG' => 'English',
            'NL' => 'Dutch',
            'ZH' => 'Chinese',
            default => $languageCode !== null && trim($languageCode) !== '' ? trim($languageCode) : null,
        };
    }

    private static function normalizeLanguageCode(?string $languageCode): ?string
    {
        if ($languageCode === null) {
            return null;
        }

        $normalized = strtoupper(trim($languageCode));
        if ($normalized === '') {
            return null;
        }

        return match ($normalized) {
            'ENGLISH' => 'ENG',
            'DUTCH', 'NEDERLANDS' => 'NL',
            'CHINESE', 'MANDARIN' => 'ZH',
            default => $normalized,
        };
    }

    private static function resolvePriceKey(EventSessionPrice $price): string
    {
        return match ($price->priceTierId) {
            PriceTierId::Adult->value, PriceTierId::Single->value => 'single',
            PriceTierId::Family->value, PriceTierId::Group->value => 'group',
            PriceTierId::PayWhatYouLike->value => 'pay-what-you-like',
            default => 'tier-' . $price->priceTierId,
        };
    }
}
