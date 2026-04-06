<?php

declare(strict_types=1);

namespace App\Exceptions;

final class SchedulePageNotFoundException extends NotFoundException
{
    public function __construct(string $slug)
    {
        parent::__construct('Schedule page slug', $slug);
    }
}
