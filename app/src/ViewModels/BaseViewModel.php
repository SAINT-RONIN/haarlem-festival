<?php

declare(strict_types=1);

namespace App\ViewModels;

/**
 * Base class for public page ViewModels that need global UI data (navigation, footer) and hero section content.
 */
abstract readonly class BaseViewModel
{
    /**
     * @var array{hero_section: array<string, string>, global_ui: array<string, string|bool>}
     */
    public array $cms;

    public function __construct(
        public HeroData $heroData,
        public GlobalUiData $globalUi,
        public string $currentPage,
        array $cms,
        public bool $includeNav = false
    ) {
        $this->cms = $cms;
    }
}
