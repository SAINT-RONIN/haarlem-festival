<?php

declare(strict_types=1);

namespace App\Models;

final readonly class DancePageData
{
    public function __construct(
        public HeroSectionContent $heroSection,
        public DanceGradientSectionContent $gradientSection,
        public DanceIntroSectionContent $introSection,
        public DanceExperienceSectionContent $experienceSection,
        public GlobalUiContent $globalUiContent,
    ) {
    }
}