<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

use App\Helpers\ImageHelper;

final readonly class StoryHighlightsSectionData
{
    private const DEFAULT_HEADING = 'Story highlights';

    /**
     * @param StoryHighlightData[] $items
     */
    public function __construct(
        public string $heading,
        public array $items,
    ) {
    }

    public static function fromCms(array $cms): self
    {
        return new self(
            heading: ImageHelper::getStringValue($cms, 'highlights_heading', self::DEFAULT_HEADING),
            items: StoryHighlightData::fromCmsArray($cms),
        );
    }

    public function hasItems(): bool
    {
        return $this->items !== [];
    }
}
