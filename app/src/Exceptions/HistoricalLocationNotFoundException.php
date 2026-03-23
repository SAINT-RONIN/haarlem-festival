<?php

declare(strict_types=1);

namespace App\Exceptions;

final class HistoricalLocationNotFoundException extends NotFoundException
{
    public function __construct(string $locationSlug)
    {
        parent::__construct('Historical location', $locationSlug);
    }
}
