<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * ViewModel for the CMS event edit page.
 *
 * Contains all data needed to render the event edit form,
 * including pre-formatted session data.
 */
final readonly class CmsEventEditViewModel
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
     * @param int|null $artistId
     * @param int|null $restaurantId
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
        public readonly ?int    $artistId,
        public readonly ?int    $restaurantId,
        public readonly bool    $isActive,
        public readonly array   $sessions,
        public readonly array   $sessionPrices,
        public readonly array   $sessionLabels,
        public readonly ?string $cmsDetailEditUrl = null,
        public readonly ?string $successMessage = null,
        public readonly ?string $errorMessage = null,
    ) {
    }


}
