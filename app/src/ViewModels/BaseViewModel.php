<?php

declare(strict_types=1);

namespace App\ViewModels;

/**
 * Base class for public page ViewModels that need shared shell data.
 */
abstract readonly class BaseViewModel
{
    public function __construct(
        public HeroData $heroData,
        public GlobalUiData $globalUi,
        public string $currentPage,
        public bool $includeNav = false
    ) {}
}
