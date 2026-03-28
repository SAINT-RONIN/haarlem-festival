<?php

declare(strict_types=1);

namespace App\Tickets\Interfaces;

use App\DTOs\Tickets\QrCodeMatrix;

/**
 * Generates QR matrices for ticket codes.
 */
interface IQrCodeGenerator
{
    public function generate(string $payload): QrCodeMatrix;
}
