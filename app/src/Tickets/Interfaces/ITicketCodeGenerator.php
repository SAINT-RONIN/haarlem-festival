<?php

declare(strict_types=1);

namespace App\Tickets\Interfaces;

interface ITicketCodeGenerator
{
    public function generate(): string;
}
