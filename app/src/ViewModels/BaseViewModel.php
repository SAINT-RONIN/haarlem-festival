<?php

declare(strict_types=1);

namespace App\ViewModels;

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
