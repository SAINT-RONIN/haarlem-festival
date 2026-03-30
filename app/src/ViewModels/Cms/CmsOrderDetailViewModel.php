<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

use App\DTOs\Cms\CmsOrderDetailDto;
use App\DTOs\Cms\CmsOrderItemDto;
use App\DTOs\Cms\CmsOrderPaymentDto;
use App\DTOs\Cms\CmsOrderTicketDto;

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
        public ?string           $successMessage,
        public ?string           $errorMessage,
    ) {
    }
}
