<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\DancePageData;

interface IDanceService
{
    public function getDancePageData(): DancePageData;
}