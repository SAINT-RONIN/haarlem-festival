<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

final readonly class StorytellingDetailNavLinkData
{
    public function __construct(
        public string $href,
        public string $label,
        public bool $isActive,
    ) {
    }
}
