<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

/**
 * Represents a single nav link rendered inside the Storytelling detail hero overlay.
 * The reason for this is because the detail hero nav must know which link is active at mapping time, and a typed object makes it impossible to accidentally omit the active flag in the view.
 */
final readonly class StorytellingDetailNavLinkData
{
    public function __construct(
        public string $href,
        public string $label,
        public bool $isActive,
    ) {}
}
