<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Checkout\OrderWithDetails;
use App\DTOs\Cms\CmsOrderDetailDto;
use App\DTOs\Cms\CmsOrderItemDto;
use App\DTOs\Cms\CmsOrderPaymentDto;
use App\DTOs\Cms\CmsOrderTicketDto;
use App\Models\Invoice;
use App\Repositories\Interfaces\ICmsOrdersRepository;
use App\Repositories\Interfaces\IInvoiceRepository;
use App\Repositories\Interfaces\IMediaAssetRepository;
use App\Services\Interfaces\ICmsOrdersService;

/**
 * CMS-side order management: read-only listing with joined user, item, and payment data.
 *
 * Delegates entirely to the orders repository; exists so controllers depend on a
 * service interface rather than a repository directly.
 */
class CmsOrdersService implements ICmsOrdersService
{
    public function __construct(
        private readonly ICmsOrdersRepository $ordersRepository,
        private readonly IInvoiceRepository $invoiceRepository,
        private readonly IMediaAssetRepository $mediaAssetRepository,
    ) {
    }

    /**
     * Returns all orders joined with their user, item, and payment data.
     *
     * When $statusFilter is null every order is returned regardless of status.
     * When a status string is provided (e.g. "paid", "pending") only orders with
     * that status are included. This is used to populate the orders list in the CMS.
     *
     * @return OrderWithDetails[]
     */
    public function getOrdersWithDetails(?string $statusFilter = null): array
    {
        return $this->ordersRepository->findOrdersWithDetails($statusFilter);
    }

    /**
     * Looks up the file path of the PDF attached to an invoice.
     *
     * Returns null in two different situations — both are valid:
     * 1. The order has no invoice record yet (fulfillment hasn't run).
     * 2. The invoice exists but its PDF hasn't been generated yet.
     * The null-safe ?-> on the last line also handles the case where the media
     * asset row was deleted without the invoice record being cleaned up.
     */
    private function resolveInvoicePdfPath(?Invoice $invoice): ?string
    {
        if ($invoice === null || $invoice->pdfAssetId === null) {
            return null;
        }

        $asset = $this->mediaAssetRepository->findById($invoice->pdfAssetId);

        // Returns null when the asset row is missing (deleted externally without cleaning invoice FK).
        return $asset?->filePath;
    }

    /**
     * Returns all the data needed to render the order detail page, bundled into one array.
     *
     * It returns null instead of throwing when the order doesn't exist because the
     * controller uses the null to send a 404 response — not every missing order is an error.
     *
     * @return array{
     *     order: CmsOrderDetailDto,
     *     items: CmsOrderItemDto[],
     *     payments: CmsOrderPaymentDto[],
     *     tickets: CmsOrderTicketDto[],
     *     invoice: ?Invoice,
     *     invoicePdfPath: ?string
     * }|null
     */
    public function getOrderDetail(int $orderId): ?array
    {
        $order = $this->ordersRepository->findOrderById($orderId);
        if ($order === null) {
            return null;
        }

        $invoice = $this->invoiceRepository->findByOrderId($orderId);
        $invoicePdfPath = $this->resolveInvoicePdfPath($invoice);

        return [
            'order'          => $order,
            'items'          => $this->ordersRepository->findOrderItems($orderId),
            'payments'       => $this->ordersRepository->findOrderPayments($orderId),
            'tickets'        => $this->ordersRepository->findOrderTickets($orderId),
            'invoice'        => $invoice,
            'invoicePdfPath' => $invoicePdfPath,
        ];
    }
}
