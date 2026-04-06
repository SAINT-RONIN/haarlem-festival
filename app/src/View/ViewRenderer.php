<?php

declare(strict_types=1);

namespace App\View;

/**
 * Renders PHP view files with an isolated local scope.
 */
final class ViewRenderer
{
    /**
     * @param array<string, mixed> $locals
     */
    public static function render(string $viewPath, array $locals = []): void
    {
        (static function (string $__viewPath, array $__locals): void {
            extract($__locals, EXTR_SKIP);
            require $__viewPath;
        })($viewPath, $locals);
    }
}
