<?php

declare(strict_types=1);

namespace App\Enums;

enum EventType: string
{
    case JAZZ = 'jazz';
    case DANCE = 'dance';
    case STORYTELLING = 'storytelling';
    case HISTORY = 'history';
    case RESTAURANT = 'restaurant';
}
