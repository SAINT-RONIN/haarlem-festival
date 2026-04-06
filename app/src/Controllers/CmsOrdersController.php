<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\NotFoundException;
use App\Mappers\CmsOrdersMapper;
use App\Services\Interfaces\ICmsOrdersService;
use App\Services\Interfaces\ISessionService;
use App\Services\Interfaces\ITicketFulfillmentService;

/**
 * CMS controller for viewing customer orders and triggering ticket resends.
 *
 * Provides a read-only list of orders with optional payment-status filtering
 * for admin review and support purposes. The one write action is resendTickets(),
 * which lets admins force a re-delivery of the ticket email after a failed send.
 */
class CmsOrdersController extends CmsBaseController
{
    /**
     * @param ICmsOrdersService         $ordersService       Read-only order queries and detail loading.
     * @param ITicketFulfillmentService  $fulfillmentService  Ticket PDF generation and email delivery.
     * @param ISessionService            $sessionService      Session, CSRF, and flash-message support.
     */
    public function __construct(
        private readonly ICmsOrdersService $ordersService,
        private readonly ITicketFulfillmentService $fulfillmentService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    /**
     * Displays the orders list with optional payment-status filtering.
     * GET /cms/orders
     */
    public function index(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $currentView = 'orders';
            $statusFilter = $this->readStringQueryParam('status');
            $viewModel = $this->buildOrdersViewModel($statusFilter);
            require __DIR__ . '/../Views/pages/cms/orders.php';
        });
    }

    /**
     * Displays a single order's full detail page.
     * GET /cms/orders/{id}
     */
    public function detail(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $currentView = 'orders';
            $viewModel = $this->buildOrderDetailViewModel($id);
            require __DIR__ . '/../Views/pages/cms/order-detail.php';
        });
    }

    /**
     * Re-runs the ticket fulfillment flow and redirects back to the order detail page.
     *
     * Clears the prior send state so the email is delivered even if it was already sent
     * once. Existing ticket rows and PDFs are reused — only the email is re-sent.
     * Intended for support use when the original delivery failed (e.g. QR library error).
     *
     * POST /cms/orders/{id}/resend-tickets
     *
     * @param int $id The order ID to resend tickets for.
     */
    public function resendTickets(int $id): void
    {
        $this->handleCmsValidationRequest(function () use ($id): void {
            $this->fulfillmentService->resendTicketEmailForOrder($id);
            $this->redirectWithFlash('Ticket email resent successfully.', 'success', "/cms/orders/{$id}");
        }, "/cms/orders/{$id}");
    }

    /** Fetches orders from the service and maps them to the list view model. */
    private function buildOrdersViewModel(?string $statusFilter): \App\ViewModels\Cms\CmsOrdersListViewModel
    {
        $ordersData = $this->ordersService->getOrdersWithDetails($statusFilter);

        return CmsOrdersMapper::toListViewModel(
            $ordersData,
            $statusFilter ?? '',
            $this->sessionService->consumeFlash('success'),
            $this->sessionService->consumeFlash('error'),
        );
    }

    /** Fetches a single order's detail data and maps it to the detail view model. */
    private function buildOrderDetailViewModel(int $orderId): \App\ViewModels\Cms\CmsOrderDetailViewModel
    {
        $data = $this->ordersService->getOrderDetail($orderId);
        if ($data === null) {
            throw new NotFoundException('Order', $orderId);
        }

        return CmsOrdersMapper::toDetailViewModel(
            $data,
            $this->sessionService->consumeFlash('success'),
            $this->sessionService->consumeFlash('error'),
        );
    }
}
