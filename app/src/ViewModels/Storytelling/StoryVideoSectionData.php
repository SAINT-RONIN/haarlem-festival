<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

use App\Helpers\ImageHelper;

final readonly class StoryVideoSectionData
{
    private const DEFAULT_HEADING = 'A moment from the show';
    private const DEFAULT_PLACEHOLDER = 'Video coming soon';

    public function __construct(
        public string $heading,
        public string $url,
        public string $placeholderText,
    ) {
    }

    public static function fromCms(array $cms): self
    {
        return new self(
            heading: ImageHelper::getStringValue($cms, 'video_heading', self::DEFAULT_HEADING),
            url: (string)($cms['video_url'] ?? ''),
            placeholderText: ImageHelper::getStringValue($cms, 'video_placeholder', self::DEFAULT_PLACEHOLDER),
        );
    }

    public function hasVideo(): bool
    {
        return $this->url !== '';
    }
}
