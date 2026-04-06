<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\DTOs\Tickets\QrCodeMatrix;
use App\Exceptions\TicketQrCodeException;
use App\Infrastructure\Interfaces\IQrCodeGenerator;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

/**
 * Standards-compliant QR generator for ticket codes.
 */
final class QrCodeGenerator implements IQrCodeGenerator
{
    private const FIXED_VERSION = 4;

    public function generate(string $payload): QrCodeMatrix
    {
        $normalizedPayload = $this->normalizePayload($payload);

        try {
            $options = new QROptions([
                'version' => self::FIXED_VERSION,
                'eccLevel' => EccLevel::M,
                'addQuietzone' => false,
            ]);

            $matrix = (new QRCode($options))
                ->addByteSegment($normalizedPayload)
                ->getQRMatrix()
                ->getMatrix(true);

            return new QrCodeMatrix(
                size: count($matrix),
                modules: $matrix,
            );
        } catch (\Throwable $error) {
            throw new TicketQrCodeException('Ticket QR payload could not be encoded.', 0, $error);
        }
    }

    private function normalizePayload(string $payload): string
    {
        $normalizedPayload = strtoupper(trim($payload));

        if ($normalizedPayload === '') {
            throw new TicketQrCodeException('Ticket QR payload cannot be empty.');
        }

        if (!mb_check_encoding($normalizedPayload, 'ASCII')) {
            throw new TicketQrCodeException('Ticket QR payload must be ASCII.');
        }

        return $normalizedPayload;
    }
}
