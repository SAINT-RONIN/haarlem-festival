<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Time-of-day filter options for the schedule page: morning, afternoon, evening.
 */
enum TimeRange: string
{
    case Morning = 'morning';
    case Afternoon = 'afternoon';
    case Evening = 'evening';
}
