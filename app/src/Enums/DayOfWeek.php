<?php

declare(strict_types=1);

namespace App\Enums;

enum DayOfWeek: int
{
    case Sunday = 0;
    case Monday = 1;
    case Tuesday = 2;
    case Wednesday = 3;
    case Thursday = 4;
    case Friday = 5;
    case Saturday = 6;

    /**
     * @return list<string>
     */
    public static function names(): array
    {
        return [
            self::Sunday->name,
            self::Monday->name,
            self::Tuesday->name,
            self::Wednesday->name,
            self::Thursday->name,
            self::Friday->name,
            self::Saturday->name,
        ];
    }
}
