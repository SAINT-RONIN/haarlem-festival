<?php

declare(strict_types=1);

namespace App\Enums;

enum PageStatus: string
{
    case Published = 'Published';
    case Draft = 'Draft';
}
