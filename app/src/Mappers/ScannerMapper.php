<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Domain\Scanner\TicketScanDetail;

/**
 * Maps scanner DTOs to JSON response arrays.
 */
final class ScannerMapper
{
    /**
     * @return array<string, mixed>
     */
    public static function toScanSuccessResponse(TicketScanDetail $detail): array
    {
        return [
            'success' => true,
            'ticketCode' => $detail->ticketCode,
            'eventTitle' => $detail->eventTitle,
            'sessionDateTime' => $detail->sessionDateTime,
            'durationMinutes' => $detail->durationMinutes,
            'venueName' => $detail->venueName,
            'orderNumber' => $detail->orderNumber,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function toAlreadyScannedResponse(TicketScanDetail $detail, string $scannedAt): array
    {
        return [
            'success' => false,
            'alreadyScanned' => true,
            'scannedAt' => $scannedAt,
            'eventTitle' => $detail->eventTitle,
            'venueName' => $detail->venueName,
            'ticketCode' => $detail->ticketCode,
        ];
    }
}
