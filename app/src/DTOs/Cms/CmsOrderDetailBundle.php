<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

use App\Models\Invoice;

/**
 * Bundles all data needed to render the CMS order detail page.
 */
readonly class CmsOrderDetailBundle
{
    /**
     * @param CmsOrderItemDto[]    $items
     * @param CmsOrderPaymentDto[] $payments
     * @param CmsOrderTicketDto[]  $tickets
     */
    public function __construct(
        public CmsOrderDetailDto $order,
        public array $items,
        public array $payments,
        public array $tickets,
        public ?Invoice $invoice,
        public ?string $invoicePdfPath,
    ) {
    }
}
