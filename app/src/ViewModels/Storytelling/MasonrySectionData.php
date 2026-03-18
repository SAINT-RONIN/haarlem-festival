<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

final readonly class MasonrySectionData
{
    public function __construct(
        public string $headingText,
        public array $images,
    ) {
    }
}
