<?php

declare(strict_types=1);

namespace App\Models;

final readonly class DanceExperienceSectionContent
{
    /**
     * @param string[] $imageUrls
     */
    public function __construct(
        public ?string $title,
        public array $imageUrls,
    ) {
    }

    public static function fromRawArray(array $raw): self
    {
        $images = $raw['experience_images'] ?? [];

        if (!is_array($images)) {
            $images = [];
        }

        return new self(
            title: $raw['experience_title'] ?? null,
            imageUrls: array_values(array_map('strval', $images)),
        );
    }
}