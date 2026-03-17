<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

use App\Helpers\ImageHelper;

final readonly class MasonrySectionData
{
    private const DEFAULT_HEADING = 'Captured storytelling moments';
    private const TOTAL_IMAGES = 12;
    private const IMAGE_KEY_PATTERN = 'masonry_image_%02d';

    public function __construct(
        public string $headingText,
        public array $images,
    ) {
    }

    public static function fromCms(array $content): self
    {
        return new self(
            headingText: ImageHelper::getStringValue($content, 'masonry_heading', self::DEFAULT_HEADING),
            images: self::buildImages($content),
        );
    }

    /**
     * @return MasonryImageData[]
     */
    private static function buildImages(array $content): array
    {
        $images = [];

        for ($index = 1; $index <= self::TOTAL_IMAGES; $index++) {
            $key = sprintf(self::IMAGE_KEY_PATTERN, $index);
            $path = (string)($content[$key] ?? '');
            $images[] = MasonryImageData::fromPath($path, $index - 1);
        }

        return $images;
    }
}
