<?php

declare(strict_types=1);

namespace App\ViewModels\History;

/**
 * View model for the "Important information about the tour" section.
 */
final readonly class ImportantInfoAboutTour
{
    /**
     * @param string[] $infoItems
     */
    public function __construct(
        public string $headingText,
        public array $infoItems,
    ) {}
}
