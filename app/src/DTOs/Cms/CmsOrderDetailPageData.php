<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

use App\Models\Invoice;

/**
 * Page data for the CMS order detail page: order header, line items, payments, tickets, and optional invoice.
 */
final readonly class CmsOrderDetailPageData
{
    /**
     * @param CmsOrderItemData[]    $items
     * @param CmsOrderPaymentData[] $payments
     * @param CmsOrderTicketData[]  $tickets
     */
    public function __construct(
        public CmsOrderDetailData $order,
        public array $items,
        public array $payments,
        public array $tickets,
        public ?Invoice $invoice,
        public ?string $invoicePdfPath,
    ) {}
}
