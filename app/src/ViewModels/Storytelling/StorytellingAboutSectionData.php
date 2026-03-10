<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

use App\Helpers\CmsOutputHelper;
use App\Helpers\ImageHelper;

final readonly class StorytellingAboutSectionData
{
    public function __construct(
        public string $heading,
        public string $bodyHtml,
        public string $image1Url,
        public string $image2Url,
    ) {
    }

    public static function fromData(string $fallbackHeading, string $aboutBodyHtml, array $cms): self
    {
        return new self(
            heading: ImageHelper::getStringValue($cms, 'about_heading', $fallbackHeading),
            bodyHtml: CmsOutputHelper::html($aboutBodyHtml),
            image1Url: ImageHelper::validatePath((string)($cms['about_image_1'] ?? '')),
            image2Url: ImageHelper::validatePath((string)($cms['about_image_2'] ?? '')),
        );
    }
}
