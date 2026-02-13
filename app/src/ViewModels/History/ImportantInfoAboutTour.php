<?php

declare(strict_types=1);

namespace App\ViewModels\History;

final readonly class ImportantInfoAboutTour
{
    /**
     * @param string[] $infoItems
     */
    public function __construct(
        public string $headingText,
        public array $infoItems,
    ) {
    }
}
