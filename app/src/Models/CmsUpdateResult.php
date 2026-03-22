<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Typed result returned by CmsEditService::updatePageItems().
 */
final readonly class CmsUpdateResult
{
    /**
     * @param string[] $errors
     */
    public function __construct(
        public bool $success,
        public int $updatedCount,
        public array $errors,
    ) {}
}
