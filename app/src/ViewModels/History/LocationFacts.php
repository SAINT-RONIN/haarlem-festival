<?php

declare(strict_types=1);

namespace App\ViewModels\History;

/**
 * View model for the Historical location facts section containing interesting facts about the location.
 */
final readonly class LocationFacts
{
    /**
     * @param string[] $facts
     */
    public function __construct(
        public string $headingText,
        public array $facts,
    ) {
    }
}
