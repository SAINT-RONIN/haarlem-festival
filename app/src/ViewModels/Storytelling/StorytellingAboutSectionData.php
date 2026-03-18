<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

final readonly class StorytellingAboutSectionData
{
    public function __construct(
        public string $heading,
        public string $bodyHtml,
        public string $image1Url,
        public string $image2Url,
    ) {
    }
}
