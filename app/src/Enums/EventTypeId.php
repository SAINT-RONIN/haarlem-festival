<?php

declare(strict_types=1);

namespace App\Enums;

enum EventTypeId: int
{
    case Jazz = 1;
    case Dance = 2;
    case History = 3;
    case Storytelling = 4;
    case Restaurant = 5;
}
