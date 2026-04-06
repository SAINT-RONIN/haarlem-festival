<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

use App\DTOs\Cms\CmsOrderDetailDto;
use App\DTOs\Cms\CmsOrderItemDto;
use App\DTOs\Cms\CmsOrderPaymentDto;
use App\DTOs\Cms\CmsOrderTicketDto;
use App\Models\Invoice;

/**
 * View data for the CMS order detail page (order-detail.php).
 */
final readonly class CmsOrderDetailViewModel
{
    /**
     * @param CmsOrderDetailDto    $order
     * @param CmsOrderItemDto[]    $items
     * @param CmsOrderPaymentDto[] $payments
     * @param CmsOrderTicketDto[]  $tickets
     */
    public function __construct(
        public CmsOrderDetailDto $order,
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
