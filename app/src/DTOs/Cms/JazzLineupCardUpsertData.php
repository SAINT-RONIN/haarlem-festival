<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * Typed carrier for the CMS jazz lineup-card create/update form fields.
 */
final readonly class JazzLineupCardUpsertData
{
    public function __construct(
        public string $name,
        public string $style,
        public string $cardDescription,
        public ?int $imageAssetId,
        public int $cardSortOrder,
        public bool $isActive,
    ) {}
}
