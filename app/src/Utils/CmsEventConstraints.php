<?php

declare(strict_types=1);

namespace App\Utils;

final class CmsEventConstraints
{
    public const MAX_LABELS_PER_SESSION = 6;
    public const MAX_LABEL_LENGTH = 60;

    private function __construct()
    {
    }
}
