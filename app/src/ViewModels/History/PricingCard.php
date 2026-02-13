<?php

declare(strict_types=1);

namespace App\ViewModels\History;

/**
 * DTO for single pricing card data.
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
        public array $descriptionItems
    ) {
    }
}
