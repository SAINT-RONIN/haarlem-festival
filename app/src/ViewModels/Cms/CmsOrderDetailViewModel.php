<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

use App\DTOs\Cms\CmsOrderDetailData;
use App\DTOs\Cms\CmsOrderItemData;
use App\DTOs\Cms\CmsOrderPaymentData;
use App\DTOs\Cms\CmsOrderTicketData;
use App\Models\Invoice;

/**
 * View data for the CMS order detail page (order-detail.php).
 */
final readonly class CmsOrderDetailViewModel
{
    /**
     * @param CmsOrderDetailData    $order
     * @param CmsOrderItemData[]    $items
     * @param CmsOrderPaymentData[] $payments
     * @param CmsOrderTicketData[]  $tickets
     */
    public function __construct(
        public CmsOrderDetailData $order,
        public array             $items,
        public array             $payments,
        public array             $tickets,
        public ?Invoice          $invoice,
        public ?string           $invoicePdfPath,
        public ?string           $successMessage,
        public ?string           $errorMessage,
    ) {
    }
}
