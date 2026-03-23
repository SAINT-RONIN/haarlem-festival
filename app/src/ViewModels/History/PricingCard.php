<?php

declare(strict_types=1);

namespace App\ViewModels\History;

/**
 * View model for a single pricing card (e.g. Single ticket, Group ticket).
 */
final readonly class PricingCard
{
    /**
     * @param string[] $descriptionItems
     */
    public function __construct(
        public string $icon,
        public string $title,
        public string $price,
        public array $descriptionItems,
    ) {
    }
}
