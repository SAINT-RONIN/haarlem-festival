<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * CMS page publication states: Published or Draft.
 */
enum PageStatus: string
{
    case Published = 'Published';
    case Draft = 'Draft';
}
