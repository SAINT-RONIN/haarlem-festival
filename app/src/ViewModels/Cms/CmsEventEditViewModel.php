<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

use App\Models\EventSession;

/**
 * ViewModel for the CMS event edit page.
 *
 * Contains all data needed to render the event edit form,
 * including pre-formatted session data.
 */
class CmsEventEditViewModel
{
    /**
     * @param int $eventId
     * @param string $title
     * @param string $shortDescription
     * @param string $longDescriptionHtml
     * @param int $eventTypeId
     * @param string $eventTypeName
     * @param string $eventTypeSlug
     * @param int|null $venueId
     * @param string|null $venueName
     * @param bool $isActive
     * @param CmsEventSessionViewModel[] $sessions
     * @param array<int, array{PriceTierId: int, TierName: string, Price: string, CurrencyCode: string}> $sessionPrices
     * @param array<int, array{EventSessionLabelId: int, LabelText: string}> $sessionLabels
     */
    public function __construct(
        public readonly int     $eventId,
        public readonly string  $title,
        public readonly string  $shortDescription,
        public readonly string  $longDescriptionHtml,
        public readonly int     $eventTypeId,
        public readonly string  $eventTypeName,
        public readonly string  $eventTypeSlug,
        public readonly ?int    $venueId,
        public readonly ?string $venueName,
        public readonly bool    $isActive,
        public readonly array   $sessions,
        public readonly array   $sessionPrices,
        public readonly array   $sessionLabels,
    ) {
    }

    /**
     * Creates a ViewModel from event data array and sessions.
     *
     * @param array $eventData Event data from repository
     * @param EventSession[] $sessions EventSession models from repository
     * @param array<int, array> $pricesData Prices keyed by session ID
     * @param array<int, array> $labelsData Labels keyed by session ID
     */
    public static function fromData(
        array $eventData,
        array $sessions,
        array $pricesData = [],
        array $labelsData = []
    ): self {
        $eventTitle = (string)($eventData['Title'] ?? '');
        $eventTypeSlug = (string)($eventData['EventTypeSlug'] ?? 'default');

        $sessionViewModels = array_map(
            fn (EventSession $session) => CmsEventSessionViewModel::fromEventSession(
                $session,
                $eventTitle,
                $eventTypeSlug
            ),
            $sessions
        );

        return new self(
            eventId: (int)$eventData['EventId'],
            title: (string)$eventData['Title'],
            shortDescription: (string)($eventData['ShortDescription'] ?? ''),
            longDescriptionHtml: (string)($eventData['LongDescriptionHtml'] ?? '<p></p>'),
            eventTypeId: (int)$eventData['EventTypeId'],
            eventTypeName: (string)($eventData['EventTypeName'] ?? ''),
            eventTypeSlug: (string)($eventData['EventTypeSlug'] ?? ''),
            venueId: isset($eventData['VenueId']) ? (int)$eventData['VenueId'] : null,
            venueName: $eventData['VenueName'] ?? null,
            isActive: (bool)($eventData['IsActive'] ?? true),
            sessions: $sessionViewModels,
            sessionPrices: $pricesData,
            sessionLabels: $labelsData,
        );
    }

    /**
     * Gets prices for a specific session.
     *
     * @return array<array{PriceTierId: int, TierName: string, Price: string, CurrencyCode: string}>
     */
    public function getPricesForSession(int $sessionId): array
    {
        return $this->sessionPrices[$sessionId] ?? [];
    }

    /**
     * Gets labels for a specific session.
     *
     * @return array<array{EventSessionLabelId: int, LabelText: string}>
     */
    public function getLabelsForSession(int $sessionId): array
    {
        return $this->sessionLabels[$sessionId] ?? [];
    }
}
