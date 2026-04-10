<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * Read-only projection of a single order line item for the CMS order detail page.
 */
final readonly class CmsOrderItemData
{
    public float $lineTotal;

    public function __construct(
        public int     $orderItemId,
        public ?string $eventTitle,
        public ?string $venueName,
        public ?string $sessionDateTime,
        public int     $quantity,
        public float   $unitPrice,
        public float   $vatRate,
        public float   $donationAmount,
        public ?string $passName,
    ) {
        $this->lineTotal = ($this->quantity * $this->unitPrice) + $this->donationAmount;
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            orderItemId: (int) $row['OrderItemId'],
            eventTitle: isset($row['EventTitle']) ? (string) $row['EventTitle'] : null,
            venueName: isset($row['VenueName']) ? (string) $row['VenueName'] : null,
            sessionDateTime: isset($row['SessionDateTime']) ? (string) $row['SessionDateTime'] : null,
            quantity: (int) $row['Quantity'],
            unitPrice: (float) $row['UnitPrice'],
            vatRate: (float) ($row['VatRate'] ?? 0),
            donationAmount: (float) ($row['DonationAmount'] ?? 0),
            passName: isset($row['PassName']) ? (string) $row['PassName'] : null,
        );
    }
}
