<?php

declare(strict_types=1);

namespace App\Infrastructure\Interfaces;

use App\DTOs\Domain\Tickets\QrCodeMatrix;

/**
 * Generates QR matrices for ticket codes.
 */
interface IQrCodeGenerator
{
    public function generate(string $payload): QrCodeMatrix;
}
