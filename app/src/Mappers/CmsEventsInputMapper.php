<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Cms\EventSessionUpsertData;
use App\DTOs\Cms\EventUpsertData;
use App\Helpers\SlugHelper;

/**
 * Maps sanitized CMS event form input into typed upsert DTOs.
 *
 * fromEventPostRequest() and fromSessionPostRequest() read directly from $_POST so
 * controllers do not need to know which fields exist in each form.
 */
final class CmsEventsInputMapper
{
    /** Reads and sanitizes the event form directly from $_POST. */
    public static function fromEventPostRequest(): EventUpsertData
    {
        return self::fromEventFormInput([
            'EventTypeId'                => self::postInt('EventTypeId'),
            'Title'                      => self::postString('Title'),
            'ShortDescription'           => self::postString('ShortDescription', 1000),
            'LongDescriptionHtml'        => self::postString('LongDescriptionHtml', 65535),
            'FeaturedImageAssetId'       => self::postInt('FeaturedImageAssetId'),
            'VenueId'                    => self::postInt('VenueId'),
            'ArtistId'                   => self::postInt('ArtistId'),
            'IsActive'                   => self::postBool('IsActive'),
            'RestaurantStars'            => self::postInt('RestaurantStars'),
            'RestaurantCuisine'          => self::postString('RestaurantCuisine'),
            'RestaurantShortDescription' => self::postString('RestaurantShortDescription'),
        ]);
    }

    /** Reads and sanitizes the session form directly from $_POST. */
    public static function fromSessionPostRequest(?int $eventIdOverride = null): EventSessionUpsertData
    {
        return self::fromSessionFormInput([
            'EventId'                   => self::postInt('EventId'),
            'StartDateTime'             => self::postString('StartDateTime'),
            'EndDateTime'               => self::postString('EndDateTime'),
            'CapacityTotal'             => self::postInt('CapacityTotal'),
            'CapacitySingleTicketLimit' => self::postInt('CapacitySingleTicketLimit'),
            'HallName'                  => self::postString('HallName'),
            'SessionType'               => self::postString('SessionType'),
            'DurationMinutes'           => self::postInt('DurationMinutes'),
            'LanguageCode'              => self::postString('LanguageCode'),
            'MinAge'                    => self::postInt('MinAge'),
            'MaxAge'                    => self::postInt('MaxAge'),
            'ReservationRequired'       => self::postBool('ReservationRequired'),
            'IsFree'                    => self::postBool('IsFree'),
            'Notes'                     => self::postString('Notes', 2000),
            'HistoryTicketLabel'        => self::postString('HistoryTicketLabel'),
            'CtaLabel'                  => self::postString('CtaLabel'),
            'CtaUrl'                    => self::postString('CtaUrl', 2048),
            'IsCancelled'               => self::postBool('IsCancelled'),
            'IsActive'                  => self::postBool('IsActive'),
        ], $eventIdOverride);
    }

    /**
     * @param array<string, mixed> $input
     */
    public static function fromEventFormInput(array $input): EventUpsertData
    {
        $title = (string) ($input['Title'] ?? '');

        return new EventUpsertData(
            eventTypeId: self::intOrDefault($input['EventTypeId'] ?? null),
            title: $title,
            shortDescription: (string) ($input['ShortDescription'] ?? ''),
            longDescriptionHtml: (string) ($input['LongDescriptionHtml'] ?? '<p></p>'),
            featuredImageAssetId: self::intOrNull($input['FeaturedImageAssetId'] ?? null),
            venueId: self::intOrNull($input['VenueId'] ?? null),
            artistId: self::intOrNull($input['ArtistId'] ?? null),
            isActive: (bool) ($input['IsActive'] ?? false),
            slug: SlugHelper::generate($title),
            restaurantStars: isset($input['RestaurantStars']) && $input['RestaurantStars'] !== '' ? (int) $input['RestaurantStars'] : null,
            restaurantCuisine: self::stringOrNull($input['RestaurantCuisine'] ?? null),
            restaurantShortDescription: self::stringOrNull($input['RestaurantShortDescription'] ?? null),
        );
    }

    /**
     * @param array<string, mixed> $input
     */
    public static function fromSessionFormInput(array $input, ?int $eventIdOverride = null): EventSessionUpsertData
    {
        return new EventSessionUpsertData(
            eventId: $eventIdOverride ?? self::intOrDefault($input['EventId'] ?? null),
            startDateTime: (string) ($input['StartDateTime'] ?? ''),
            endDateTime: (string) ($input['EndDateTime'] ?? ''),
            capacityTotal: self::intOrNull($input['CapacityTotal'] ?? null) ?? 100,
            capacitySingleTicketLimit: self::intOrNull($input['CapacitySingleTicketLimit'] ?? null) ?? 100,
            hallName: self::stringOrNull($input['HallName'] ?? null),
            sessionType: self::stringOrNull($input['SessionType'] ?? null),
            durationMinutes: self::intOrNull($input['DurationMinutes'] ?? null),
            languageCode: self::stringOrNull($input['LanguageCode'] ?? null),
            minAge: self::intOrNull($input['MinAge'] ?? null),
            maxAge: self::intOrNull($input['MaxAge'] ?? null),
            reservationRequired: (bool) ($input['ReservationRequired'] ?? false),
            isFree: (bool) ($input['IsFree'] ?? false),
            notes: (string) ($input['Notes'] ?? ''),
            historyTicketLabel: self::stringOrNull($input['HistoryTicketLabel'] ?? null),
            ctaLabel: self::stringOrNull($input['CtaLabel'] ?? null),
            ctaUrl: self::stringOrNull($input['CtaUrl'] ?? null),
            isCancelled: (bool) ($input['IsCancelled'] ?? false),
            isActive: (bool) ($input['IsActive'] ?? true),
        );
    }

    private static function intOrNull(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
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

    private static function postString(string $key, int $maxLength = 255): ?string
    {
        $value = trim((string) ($_POST[$key] ?? ''));
        if ($value === '') {
            return null;
        }

        return mb_substr($value, 0, $maxLength);
    }

    private static function postInt(string $key): ?int
    {
        $value = trim((string) ($_POST[$key] ?? ''));
        if ($value === '' || !is_numeric($value)) {
            return null;
        }

        return (int) $value;
    }

    private static function postBool(string $key): bool
    {
        $raw = $_POST[$key] ?? '';
        return $raw === '1' || $raw === 'on';
    }
}
