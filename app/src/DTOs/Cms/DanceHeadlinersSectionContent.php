<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * Carries CMS item values for the Dance headliners_section.
 */
final readonly class DanceHeadlinersSectionContent
{
    public function __construct(
        public ?string $headlinersHeading,
    ) {}
}
