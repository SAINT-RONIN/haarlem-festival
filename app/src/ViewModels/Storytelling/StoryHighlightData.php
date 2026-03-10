<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

use App\Helpers\ImageHelper;

final readonly class StoryHighlightData
{
    private const MAX_HIGHLIGHTS = 3;

    public function __construct(
        public string $imageUrl,
        public string $title,
        public string $description,
    ) {
    }

    /**
     * @return StoryHighlightData[]
     */
    public static function fromCmsArray(array $cms): array
    {
        $highlights = [];

        for ($i = 1; $i <= self::MAX_HIGHLIGHTS; $i++) {
            $title = $cms["highlight_{$i}_title"] ?? '';
            if (empty($title)) {
                continue;
            }

            $highlights[] = new self(
                imageUrl: ImageHelper::validatePath((string)($cms["highlight_{$i}_image"] ?? '')),
                title: $title,
                description: $cms["highlight_{$i}_description"] ?? '',
            );
        }

        return $highlights;
    }
}
