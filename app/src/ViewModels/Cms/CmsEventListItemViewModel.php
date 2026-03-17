<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

use App\Models\EventWithDetails;

/**
 * ViewModel for a single event item in the CMS events list.
 *
 * Transforms raw event data with joined fields into a typed, view-ready object.
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
        public bool    $isActive,
    ) {
    }

    public static function fromEventWithDetails(EventWithDetails $event): self
    {
        return new self(
            eventId: $event->eventId,
            title: $event->title,
            shortDescription: $event->shortDescription,
            eventTypeId: $event->eventTypeId,
            eventTypeName: $event->eventTypeName,
            eventTypeSlug: $event->eventTypeSlug,
            venueName: $event->venueName,
            sessionCount: $event->sessionCount,
            isActive: $event->isActive,
        );
    }

    /**
     * Gets a CSS class based on the event type slug.
     */
    public function getTypeClass(): string
    {
        return 'event-type-' . $this->eventTypeSlug;
    }

    /**
     * Gets the status badge text.
     */
    public function getStatusText(): string
    {
        return $this->isActive ? 'Active' : 'Inactive';
    }

    /**
     * Gets the status badge CSS class.
     */
    public function getStatusClass(): string
    {
        return $this->isActive ? 'badge-success' : 'badge-secondary';
    }
}
