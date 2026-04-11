<?php

declare(strict_types=1);

namespace App\ViewModels\Dance;

/**
 * Section data for the Dance page headliners — heading and the two featured artist cards.
 */
final readonly class HeadlinersData
{
    /**
     * @param DanceArtistCardData[] $headliners
     */
    public function __construct(
        public string $headingText,
        public array $headliners,
    ) {}
}
