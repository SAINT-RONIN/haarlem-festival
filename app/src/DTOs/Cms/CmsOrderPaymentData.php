<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * Read-only projection of a single payment record for the CMS order detail page.
 */
final readonly class CmsOrderPaymentData
{
    public function __construct(
        public int     $paymentId,
        public string  $method,
        public string  $status,
        public string  $providerRef,
        public string  $createdAtUtc,
        public ?string $paidAtUtc,
    ) {}

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            paymentId: (int) $row['PaymentId'],
            method: (string) ($row['Method'] ?? ''),
            status: (string) ($row['Status'] ?? ''),
            providerRef: (string) ($row['ProviderRef'] ?? ''),
            createdAtUtc: (string) ($row['CreatedAtUtc'] ?? ''),
            paidAtUtc: isset($row['PaidAtUtc']) ? (string) $row['PaidAtUtc'] : null,
        );
    }
}
