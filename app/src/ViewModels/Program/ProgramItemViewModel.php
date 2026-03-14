<?php

declare(strict_types=1);

namespace App\ViewModels\Program;

use App\Enums\EventTypeId;
use App\ViewModels\Age\AgeLabelFormatter;

final readonly class ProgramItemViewModel
{
    private const LANGUAGE_LABELS = [
        'nl' => 'Dutch',
        'en' => 'English',
        'de' => 'German',
        'fr' => 'French',
    ];

    public function __construct(
        public int $programItemId,
        public int $eventSessionId,
        public string $eventTitle,
        public string $locationDisplay,
        public string $dateTimeDisplay,
        public string $priceDisplay,
        public float $rawPrice,
        public int $quantity,
        public float $donationAmount,
        public string $donationDisplay,
        public string $sumDisplay,
        public string $eventTypeSlug,
        public string $eventTypeLabel,
        public string $eventTypeImageUrl,
        public bool $isPayWhatYouLike,
        public ?string $languageLabel,
        public ?string $ageLabel,
    ) {
    }

    /**
     * @param array<string, mixed> $item
     */
    public static function fromItemData(array $item): self
    {
        $basePrice = (float)$item['basePrice'];
        $quantity = (int)$item['quantity'];
        $donationAmount = (float)$item['donationAmount'];
        $eventTypeId = (int)$item['eventTypeId'];
        $eventTypeSlug = (string)$item['eventTypeSlug'];
        $isPayWhatYouLike = (bool)$item['isPayWhatYouLike'];

        $lineTotal = ($basePrice * $quantity) + $donationAmount;
        $priceDisplay = $isPayWhatYouLike ? 'Free' : self::formatPrice($basePrice);

        return new self(
            programItemId: (int)$item['programItemId'],
            eventSessionId: (int)$item['eventSessionId'],
            eventTitle: (string)$item['eventTitle'],
            locationDisplay: self::buildLocationDisplay($item['venueName'] ?? '', $item['hallName'] ?? null),
            dateTimeDisplay: self::buildDateTimeDisplay($item['startDateTime'] ?? '', $item['endDateTime'] ?? null),
            priceDisplay: $priceDisplay,
            rawPrice: $basePrice,
            quantity: $quantity,
            donationAmount: $donationAmount,
            donationDisplay: $donationAmount > 0 ? self::formatPrice($donationAmount) : '',
            sumDisplay: self::formatPrice($lineTotal),
            eventTypeSlug: $eventTypeSlug,
            eventTypeLabel: self::getEventTypeLabel($eventTypeId, (string)$item['eventTypeName']),
            eventTypeImageUrl: self::getEventTypeImageUrl($eventTypeId),
            isPayWhatYouLike: $isPayWhatYouLike,
            languageLabel: self::buildLanguageLabel($item['languageCode'] ?? null),
            ageLabel: AgeLabelFormatter::format($item['minAge'] ?? null, $item['maxAge'] ?? null),
        );
    }

    private static function getEventTypeImageUrl(int $eventTypeId): string
    {
        // Maps event type IDs to static icon filenames in assets/icons/my-program/.
        // These are design assets, not user-facing text — no DB column exists for them.
        $filename = match ($eventTypeId) {
            EventTypeId::Jazz->value => 'saxophone',
            EventTypeId::Dance->value => 'dance',
            EventTypeId::History->value => 'museum',
            EventTypeId::Storytelling->value => 'book',
            EventTypeId::Restaurant->value => 'food-meal',
            default => 'book',
        };

        return "/assets/icons/my-program/{$filename}.png";
    }

    private static function getEventTypeLabel(int $eventTypeId, string $fallback): string
    {
        return $fallback !== '' ? $fallback : (string)$eventTypeId;
    }

    private static function buildLocationDisplay(string $venueName, ?string $hallName): string
    {
        if ($venueName === '') {
            return '';
        }

        if ($hallName !== null && $hallName !== '') {
            return "{$venueName} - {$hallName}";
        }

        return $venueName;
    }

    private static function buildDateTimeDisplay(string $startDateTime, ?string $endDateTime): string
    {
        if ($startDateTime === '') {
            return '';
        }

        $start = new \DateTimeImmutable($startDateTime);
        $dayAndDate = $start->format('l, F j');
        $startTime = $start->format('H:i');

        if ($endDateTime !== null && $endDateTime !== '') {
            $end = new \DateTimeImmutable($endDateTime);
            $endTime = $end->format('H:i');
            return "{$dayAndDate} · {$startTime} - {$endTime}";
        }

        return "{$dayAndDate} · {$startTime}";
    }

    private static function buildLanguageLabel(?string $languageCode): ?string
    {
        if ($languageCode === null || $languageCode === '') {
            return null;
        }

        return self::LANGUAGE_LABELS[$languageCode] ?? $languageCode;
    }

    public static function formatPrice(float $amount): string
    {
        return '€' . number_format($amount, 2, '.', '');
    }

    /**
     * @param array{subtotal: float, taxAmount: float, total: float} $programData
     * @return array{subtotal: string, taxAmount: string, total: string}
     */
    public static function formatTotals(array $programData): array
    {
        return [
            'subtotal' => self::formatPrice((float)$programData['subtotal']),
            'taxAmount' => self::formatPrice((float)$programData['taxAmount']),
            'total' => self::formatPrice((float)$programData['total']),
        ];
    }
}
