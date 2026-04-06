<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\DTOs\Cms\GlobalUiContent;
use App\DTOs\Cms\GradientSectionContent;
use App\DTOs\Cms\HeroSectionContent;
use App\DTOs\Cms\IntroSectionContent;

/**
 * Typed access to shared/global CMS content sections.
 */
interface IGlobalContentRepository
{
    /** Fetches the global UI navigation/labels content. */
    public function findGlobalUiContent(string $pageSlug, string $sectionKey): GlobalUiContent;

    /** Fetches the hero section content for a given page. */
    public function findHeroContent(string $pageSlug): HeroSectionContent;

    /** Fetches hero content using a custom section key (not the default hero key). */
    public function findHeroContentBySection(string $pageSlug, string $sectionKey): HeroSectionContent;

    /**
     * Maps an already-fetched raw hero array to a HeroSectionContent model.
     *
     * @param array<string, mixed> $raw
     */
    public function mapHeroFromRaw(array $raw): HeroSectionContent;

    /** Fetches the gradient section content for a given page and section key. */
    public function findGradientContent(string $pageSlug, string $sectionKey): GradientSectionContent;

    /** Fetches the intro section content for a given page and section key. */
    public function findIntroContent(string $pageSlug, string $sectionKey): IntroSectionContent;
}
