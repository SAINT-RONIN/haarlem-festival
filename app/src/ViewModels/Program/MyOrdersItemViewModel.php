<?php

declare(strict_types=1);

namespace App\ViewModels\Program;

/**
 * Display-ready data for a single order card on the "My Orders" page.
 * All values are pre-formatted strings suitable for direct rendering.
 */
final readonly class MyOrdersItemViewModel
{
    public function __construct(
        public int $orderId,
        public string $orderNumber,
        public string $statusText,
        public string $statusBadgeClass,
        public string $totalAmountFormatted,
        public string $createdAtFormatted,
        public int $itemCount,
        public string $paymentStatusText,
        public string $paymentBadgeClass,
        /** @var array<int, array{ticketCode: string, url: string}> */
        public array $ticketPdfUrls,
        public bool $canRetryPayment,
        public string $retryUrl,
    ) {}
}
