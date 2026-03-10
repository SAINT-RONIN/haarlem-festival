<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

use App\Helpers\ImageHelper;

final readonly class StoryGallerySectionData
{
    private const DEFAULT_HEADING = 'Where stories come alive';
    private const GALLERY_IMAGE_COUNT = 5;

    /**
     * @param string[] $topRowImages
     * @param string[] $bottomRowImages
     */
    public function __construct(
        public string $heading,
        public array $topRowImages,
        public array $bottomRowImages,
    ) {
    }

    public static function fromCms(array $cms): self
    {
        $images = [];

        for ($i = 1; $i <= self::GALLERY_IMAGE_COUNT; $i++) {
            $images[] = ImageHelper::validatePath((string)($cms["gallery_image_{$i}"] ?? ''));
        }

        return new self(
            heading: ImageHelper::getStringValue($cms, 'gallery_heading', self::DEFAULT_HEADING),
            topRowImages: array_slice($images, 0, 3),
            bottomRowImages: array_slice($images, 3, 2),
        );
    }
}
