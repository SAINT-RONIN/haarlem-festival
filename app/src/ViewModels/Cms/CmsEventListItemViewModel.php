<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * A single event row in the CMS events list table.
 *
 * Pre-computed display values like type badge class and status text.
 */
final readonly class CmsEventListItemViewModel
{
    public function __construct(
        public int     $eventId,
        public string  $title,
        public string  $shortDescription,
        public int     $eventTypeId,
        public string  $eventTypeName,
        public string  $eventTypeSlug,
        public ?string $venueName,
        public int     $sessionCount,
        public int     $totalSoldTickets,
        public int     $totalCapacity,
        public bool    $isActive,
        public string  $typeClass,
        public string  $statusText,
        public string  $statusClass,
    ) {}
}
