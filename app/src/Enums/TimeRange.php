<?php

declare(strict_types=1);

namespace App\Enums;

enum TimeRange: string
{
    case Morning = 'morning';
    case Afternoon = 'afternoon';
    case Evening = 'evening';
}
