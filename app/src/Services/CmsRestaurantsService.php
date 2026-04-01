<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EventTypeId;
use App\Models\RestaurantDetailEvent;
use App\Repositories\Interfaces\IEventRepository;
use App\Services\Interfaces\ICmsRestaurantsService;

class CmsRestaurantsService implements ICmsRestaurantsService
{
    public function __construct(
        private readonly IEventRepository $eventRepository,
    ) {}

    /** @return RestaurantDetailEvent[] */
    public function getRestaurants(?string $search): array
    {
        $events = $this->eventRepository->findActiveRestaurantEvents();

        if ($search !== null && $search !== '') {
            $lower = strtolower($search);
            $events = array_filter(
                $events,
                fn(RestaurantDetailEvent $e) => str_contains(strtolower($e->title), $lower),
            );
        }

        return array_values($events);
    }

    public function findById(int $id): ?RestaurantDetailEvent
    {
        $event = $this->eventRepository->findById($id);
        if ($event === null || $event->eventTypeId !== EventTypeId::Restaurant->value) {
            return null;
        }

        return RestaurantDetailEvent::fromRow([
            'EventId'             => $event->eventId,
            'Slug'                => $event->slug ?? '',
            'Title'               => $event->title,
            'ShortDescription'    => $event->shortDescription ?? '',
            'LongDescriptionHtml' => $event->longDescriptionHtml ?? '',
            'FeaturedImageAssetId' => $event->featuredImageAssetId,
        ]);
    }

    /** @return array<string, string> */
    public function validateForCreate(array $data): array
    {
        return $this->validate($data);
    }

    /** @return array<string, string> */
    public function validateForUpdate(int $id, array $data): array
    {
        return $this->validate($data);
    }

    public function createRestaurant(array $data): int
    {
        return $this->eventRepository->create(array_merge($data, [
            'EventTypeId' => EventTypeId::Restaurant->value,
        ]));
    }

    public function updateRestaurant(int $id, array $data): void
    {
        $this->eventRepository->update($id, $data);
    }

    public function deleteRestaurant(int $id): void
    {
        $this->eventRepository->softDelete($id);
    }

    /** @return array<string, string> */
    private function validate(array $data): array
    {
        $errors = [];
        if (empty(trim($data['Title'] ?? ''))) {
            $errors['title'] = 'Title is required.';
        }
        if (empty(trim($data['Slug'] ?? ''))) {
            $errors['slug'] = 'Slug is required.';
        }
        return $errors;
    }
}
