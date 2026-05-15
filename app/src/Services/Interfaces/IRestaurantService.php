<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Cms\GlobalUiContent;
use App\DTOs\Domain\Pages\RestaurantPageData;
use App\DTOs\Domain\Restaurant\ReservationFormData;
use App\Models\Restaurant;

interface IRestaurantService
{
    public function getRestaurantPageData(): RestaurantPageData;

    /** @throws \App\Exceptions\RestaurantEventNotFoundException */
    public function getRestaurant(string $slug): Restaurant;

    /** @return array<string, ?string> Shared detail-page labels from CMS */
    public function getDetailLabels(): array;

    public function getGlobalUi(): GlobalUiContent;

    /**
     * @throws \App\Exceptions\RestaurantEventNotFoundException
     * @throws \App\Exceptions\ValidationException
     * @return int The new reservation ID
     */
    public function submitReservation(string $slug, ReservationFormData $formData): int;
}
