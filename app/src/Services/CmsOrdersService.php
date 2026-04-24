<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Cms\CmsOrderDetailPageData;
use App\DTOs\Domain\Checkout\OrderWithDetails;
use App\Models\Invoice;
use App\Repositories\Interfaces\ICmsOrdersRepository;
use App\Repositories\Interfaces\IInvoiceRepository;
use App\Repositories\Interfaces\IMediaAssetRepository;
use App\Services\Interfaces\ICmsOrdersService;

// Read-only CMS order listing. Exists so controllers depend on a service, not a repository.
class CmsOrdersService implements ICmsOrdersService
{
    public function __construct(
        private readonly ICmsOrdersRepository $ordersRepository,
        private readonly IInvoiceRepository $invoiceRepository,
        private readonly IMediaAssetRepository $mediaAssetRepository,
    ) {}

    /** @return OrderWithDetails[] */
    public function getOrdersWithDetails(?string $statusFilter = null): array
    {
        return $this->ordersRepository->findOrdersWithDetails($statusFilter);
    }

    private function resolveInvoicePdfPath(?Invoice $invoice): ?string
    {
        if ($invoice === null || $invoice->pdfAssetId === null) {
            return null;
        }

        $asset = $this->mediaAssetRepository->findById($invoice->pdfAssetId);

        // Returns null when the asset row is missing (deleted externally without cleaning invoice FK).
        return $asset?->filePath;
    }

    public function getOrderDetail(int $orderId): ?CmsOrderDetailPageData
    {
        $order = $this->ordersRepository->findOrderById($orderId);
        if ($order === null) {
            return null;
        }

        $invoice = $this->invoiceRepository->findByOrderId($orderId);

        return new CmsOrderDetailPageData(
            order: $order,
            items: $this->ordersRepository->findOrderItems($orderId),
            payments: $this->ordersRepository->findOrderPayments($orderId),
            tickets: $this->ordersRepository->findOrderTickets($orderId),
            invoice: $invoice,
            invoicePdfPath: $this->resolveInvoicePdfPath($invoice),
        );
    }
}
