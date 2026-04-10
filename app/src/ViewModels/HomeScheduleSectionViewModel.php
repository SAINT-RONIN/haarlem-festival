<?php

declare(strict_types=1);

namespace App\ViewModels;

/**
 * ViewModel for the home page schedule preview heading section.
 */
final readonly class HomeScheduleSectionViewModel
{
    public function __construct(
        public string $title,
        public string $subtitlePrimary,
        public string $subtitleSecondary,
    ) {}
}
