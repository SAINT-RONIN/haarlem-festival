<?php

declare(strict_types=1);

namespace App\View;

/**
 * Explicit view fragment configuration for nested template rendering.
 */
final readonly class ViewTemplate
{
    /**
     * @param array<string, mixed> $locals
     */
    public function __construct(
        public string $path,
        public array $locals = [],
    ) {
    }
}
