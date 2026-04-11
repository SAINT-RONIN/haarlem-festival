<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Domain\Pages\DancePageData;

interface IDanceService
{
    public function getDancePageData(): DancePageData;
}
