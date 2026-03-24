<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Result of a CMS content update operation — success flag, count of updated items,
 * and any validation errors. Returned by CmsEditService.
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
