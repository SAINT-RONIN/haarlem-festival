<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Integer IDs for the five festival event categories.
 *
 * Used to conditionally show/hide form fields and determine which public page an event appears on.
 */
enum EventTypeId: int
{
    case Jazz = 1;
    case Dance = 2;
    case History = 3;
    case Storytelling = 4;
    case Restaurant = 5;
}
