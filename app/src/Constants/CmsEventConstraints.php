<?php

declare(strict_types=1);

namespace App\Constants;

/**
 * Validation limits for CMS event and session management operations.
 */
final class CmsEventConstraints
{
    public const MAX_LABELS_PER_SESSION = 6;
    public const MAX_LABEL_LENGTH       = 60;
    public const MIN_STARS              = 1;
    public const MAX_STARS              = 5;

    private function __construct()
    {
    }
}
