<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Restaurant;

use App\DTOs\Cms\GlobalUiContent;
use App\Models\Restaurant;

final readonly class RestaurantDetailPageData
{
    /**
     * @param array<string, ?string> $detailLabels
     * @param string[] $validDates
     */
    public function __construct(
        public Restaurant $restaurant,
        public array $detailLabels,
        public GlobalUiContent $globalUi,
        public array $validDates,
    ) {}
}