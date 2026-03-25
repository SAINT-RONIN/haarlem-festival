<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Jazz pass coverage: Day (single festival day) or Range (multiple days / full festival).
 */
enum PassScope: string
{
    case Day = 'Day';
    case Range = 'Range';
}
