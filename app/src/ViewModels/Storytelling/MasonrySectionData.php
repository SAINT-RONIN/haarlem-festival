<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

use App\Helpers\ImageHelper;

final readonly class MasonrySectionData
{
    private const DEFAULT_HEADING = 'Captured storytelling moments';
    private const COLUMNS = 4;
    private const IMAGES_PER_COLUMN = 3;
    private const TOTAL_IMAGES = 12;
    private const IMAGE_KEY_PATTERN = 'masonry_image_%02d';

    /**
     * @param array<int, MasonryImageData[]> $columns
     */
    public function __construct(
        public string $headingText,
        public array $columns,
    ) {
    }

    public static function fromCms(array $content): self
    {
        $images = self::buildImages($content);
        $columns = self::distributeIntoColumns($images);

        return new self(
            headingText: ImageHelper::getStringValue($content, 'masonry_heading', self::DEFAULT_HEADING),
            columns: $columns,
        );
    }

    /**
     * @return MasonryImageData[]
     */
    private static function buildImages(array $content): array
    {
        $paths = self::collectImagePaths($content);
        if ($paths === []) {
            return [];
        }

        $images = [];
        foreach ($paths as $index => $path) {
            $images[] = MasonryImageData::fromPath($path, $index);
        }

        return $images;
    }

    /**
     * @return list<string>
     */
    private static function collectImagePaths(array $content): array
    {
        $paths = [];

        for ($index = 1; $index <= self::TOTAL_IMAGES; $index++) {
            $key = sprintf(self::IMAGE_KEY_PATTERN, $index);
            $path = $content[$key] ?? null;

            if (is_string($path) && $path !== '') {
                $paths[] = $path;
            }
        }

        return $paths;
    }

    /**
     * @return array<int, MasonryImageData[]>
     */
    private static function distributeIntoColumns(array $images): array
    {
        $columns = [];
        $imageIndex = 0;

        for ($col = 0; $col < self::COLUMNS; $col++) {
            $columns[$col] = [];

            for ($row = 0; $row < self::IMAGES_PER_COLUMN; $row++) {
                $columns[$col][] = $images[$imageIndex] ?? MasonryImageData::placeholder();
                $imageIndex++;
            }
        }

        return $columns;
    }
}
