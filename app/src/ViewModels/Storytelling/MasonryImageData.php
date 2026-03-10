<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

use App\Helpers\ImageHelper;

final readonly class MasonryImageData
{
    private const DEFAULT_ALT_SUFFIX = 'Storytelling moment';
    private const DEFAULT_SIZE_CLASS = 'masonry-medium';

    private const SIZE_CLASSES = [
        'masonry-tall',
        'masonry-short',
        'masonry-medium',
        'masonry-medium',
        'masonry-tall',
        'masonry-short',
        'masonry-short',
        'masonry-medium',
        'masonry-tall',
        'masonry-medium',
        'masonry-short',
        'masonry-tall',
    ];

    public function __construct(
        public string $imageUrl,
        public string $altText,
        public string $sizeClass,
    ) {
    }

    public static function fromPath(string $path, int $index): self
    {
        return new self(
            imageUrl: ImageHelper::validatePath($path),
            altText: ImageHelper::altTextFromFilename(basename($path), self::DEFAULT_ALT_SUFFIX),
            sizeClass: self::SIZE_CLASSES[$index] ?? self::DEFAULT_SIZE_CLASS,
        );
    }

    public static function placeholder(): self
    {
        return new self(
            imageUrl: ImageHelper::validatePath(''),
            altText: self::DEFAULT_ALT_SUFFIX,
            sizeClass: self::DEFAULT_SIZE_CLASS,
        );
    }
}
