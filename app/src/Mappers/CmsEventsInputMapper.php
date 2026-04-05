<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Cms\EventSessionUpsertData;
use App\DTOs\Cms\EventUpsertData;
use App\Helpers\SlugHelper;

/**
 * Maps sanitized CMS event form input into typed upsert DTOs.
 */
final class CmsEventsInputMapper
{
    /**
     * @param array<string, mixed> $input
     */
    public static function fromEventFormInput(array $input): EventUpsertData
    {
        $title = (string)($input['Title'] ?? '');

        return new EventUpsertData(
            eventTypeId: self::intOrDefault($input['EventTypeId'] ?? null),
            title: $title,
            shortDescription: (string)($input['ShortDescription'] ?? ''),
            longDescriptionHtml: (string)($input['LongDescriptionHtml'] ?? '<p></p>'),
            featuredImageAssetId: self::intOrNull($input['FeaturedImageAssetId'] ?? null),
            venueId: self::intOrNull($input['VenueId'] ?? null),
            artistId: self::intOrNull($input['ArtistId'] ?? null),
            restaurantId: self::intOrNull($input['RestaurantId'] ?? null),
            isActive: isset($input['IsActive']) && $input['IsActive'] !== '',
            slug: SlugHelper::generate($title),
        );
    }

    /**
     * @param array<string, mixed> $input
     */
    public static function fromSessionFormInput(array $input, ?int $eventIdOverride = null): EventSessionUpsertData
    {
        return new EventSessionUpsertData(
            eventId: $eventIdOverride ?? self::intOrDefault($input['EventId'] ?? null),
            startDateTime: (string)($input['StartDateTime'] ?? ''),
            endDateTime: (string)($input['EndDateTime'] ?? ''),
            capacityTotal: self::intOrNull($input['CapacityTotal'] ?? null),
            capacitySingleTicketLimit: self::intOrNull($input['CapacitySingleTicketLimit'] ?? null),
            hallName: self::stringOrNull($input['HallName'] ?? null),
            sessionType: self::stringOrNull($input['SessionType'] ?? null),
            durationMinutes: self::intOrNull($input['DurationMinutes'] ?? null),
            languageCode: self::stringOrNull($input['LanguageCode'] ?? null),
            minAge: self::intOrNull($input['MinAge'] ?? null),
            maxAge: self::intOrNull($input['MaxAge'] ?? null),
            reservationRequired: (bool)($input['ReservationRequired'] ?? false),
            isFree: (bool)($input['IsFree'] ?? false),
            notes: (string)($input['Notes'] ?? ''),
            historyTicketLabel: self::stringOrNull($input['HistoryTicketLabel'] ?? null),
            ctaLabel: self::stringOrNull($input['CtaLabel'] ?? null),
            ctaUrl: self::stringOrNull($input['CtaUrl'] ?? null),
            isCancelled: (bool)($input['IsCancelled'] ?? false),
            isActive: (bool)($input['IsActive'] ?? true),
        );
    }

    private static function intOrNull(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int)$value;
    }

    private static function intOrDefault(mixed $value): int
    {
        return self::intOrNull($value) ?? 0;
    }

    private static function stringOrNull(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $trimmed = trim($value);
        return $trimmed === '' ? null : $trimmed;
    }
}
