<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

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

    /**
     * Creates a ViewModel from a joined query result array.
     *
     * @param array{
     *     EventId: int,
     *     Title: string,
     *     ShortDescription?: string,
     *     EventTypeId: int,
     *     EventTypeName: string,
     *     EventTypeSlug: string,
     *     VenueName: ?string,
     *     SessionCount: int,
     *     IsActive: int|bool
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            eventId: (int)$data['EventId'],
            title: (string)$data['Title'],
            shortDescription: (string)($data['ShortDescription'] ?? ''),
            eventTypeId: (int)$data['EventTypeId'],
            eventTypeName: (string)$data['EventTypeName'],
            eventTypeSlug: (string)$data['EventTypeSlug'],
            venueName: $data['VenueName'] ?? null,
            sessionCount: (int)$data['SessionCount'],
            isActive: (bool)$data['IsActive'],
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
