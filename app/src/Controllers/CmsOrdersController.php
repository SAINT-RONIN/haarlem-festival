<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\NotFoundException;
use App\Mappers\CmsOrdersMapper;
use App\Services\Interfaces\ICmsOrdersService;
use App\Services\Interfaces\ISessionService;
use App\Services\Interfaces\ITicketFulfillmentService;

/**
 * CMS controller for viewing customer orders, exporting data, and resending ticket emails.
 */
class CmsOrdersController extends CmsBaseController
{
    public function __construct(
        private readonly ICmsOrdersService $ordersService,
        private readonly ITicketFulfillmentService $fulfillmentService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    public function index(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $currentView = 'orders';
            $statusFilter = $this->readStringQueryParam('status');
            $viewModel = $this->buildOrdersViewModel($statusFilter);
            require __DIR__ . '/../Views/pages/cms/orders.php';
        });
    }

    public function detail(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $currentView = 'orders';
            $viewModel = $this->buildOrderDetailViewModel($id);
            require __DIR__ . '/../Views/pages/cms/order-detail.php';
        });
    }

    // Re-sends the ticket email only (reuses existing tickets/PDFs). For support use after failed delivery.
    public function resendTickets(int $id): void
    {
        $this->handleCmsValidationRequest(function () use ($id): void {
            $this->fulfillmentService->resendTicketEmailForOrder($id);
            $this->redirectWithFlash('Ticket email resent successfully.', 'success', "/cms/orders/{$id}");
        }, "/cms/orders/{$id}");
    }

    public function exportCsv(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $orders = $this->ordersService->getOrdersWithDetails($this->readStringQueryParam('status'));

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=orders.csv');

            $out = fopen('php://output', 'w');
            fputcsv($out, ['Order #', 'User ID', 'Email', 'Items', 'Amount', 'Order Status', 'Payment Status', 'Date'], ',', '"', '');

            foreach ($orders as $o) {
                fputcsv($out, [$o->orderNumber, $o->userAccountId, $o->email, $o->itemsSummary, $o->totalAmount, $o->status, $o->paymentStatus ?? 'No payment', $o->createdAtUtc], ',', '"', '');
            }

            fclose($out);
            exit;
        });
    }

    public function exportExcel(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $orders = $this->ordersService->getOrdersWithDetails($this->readStringQueryParam('status'));

            header('Content-Type: application/vnd.ms-excel; charset=utf-8');
            header('Content-Disposition: attachment; filename=orders.xls');

            $esc = fn(string $v) => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

            echo "<table border='1'><thead><tr><th>Order #</th><th>User ID</th><th>Email</th><th>Items</th><th>Amount</th><th>Order Status</th><th>Payment Status</th><th>Date</th></tr></thead><tbody>";

            foreach ($orders as $o) {
                echo '<tr><td>' . $esc($o->orderNumber) . '</td><td>' . $o->userAccountId . '</td><td>' . $esc($o->email) . '</td><td>' . $esc($o->itemsSummary ?? '') . '</td><td>' . $esc($o->totalAmount) . '</td><td>' . $esc($o->status) . '</td><td>' . $esc($o->paymentStatus ?? 'No payment') . '</td><td>' . $esc($o->createdAtUtc) . '</td></tr>';
            }

            echo '</tbody></table>';
            exit;
        });
    }

    public function exportDetailCsv(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $data = $this->ordersService->getOrderDetail($id) ?? throw new NotFoundException('Order', $id);
            $o = $data->order;

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=order-' . $id . '.csv');

            $out = fopen('php://output', 'w');
            $w = fn(array $r) => fputcsv($out, $r, ',', '"', '');

            // Section: Order Info
            $w(['--- ORDER ---']);
            $w(['Field', 'Value']);
            $w(['Order #', $o->orderNumber]);
            $w(['Status', $o->status]);
            $w(['Subtotal', $o->subtotal]);
            $w(['VAT', $o->vatTotal]);
            $w(['Total', $o->totalAmount]);
            $w(['Created', $o->createdAtUtc]);
            $w(['Pay Before', $o->payBeforeUtc]);
            $w(['User Email', $o->userEmail]);
            $w(['Recipient', trim($o->ticketRecipientFirstName . ' ' . $o->ticketRecipientLastName)]);
            $w(['Recipient Email', $o->ticketRecipientEmail]);

            // Section: Items
            $w(['']);
            $w(['--- ITEMS ---']);
            $w(['Event', 'Venue', 'Session', 'Qty', 'Unit Price', 'VAT %', 'Donation', 'Line Total']);
            foreach ($data->items as $i) {
                $w([(string) ($i->eventTitle ?? ''), (string) ($i->venueName ?? ''), (string) ($i->sessionDateTime ?? ''), (string) $i->quantity, (string) $i->unitPrice, (string) $i->vatRate, (string) $i->donationAmount, (string) $i->lineTotal]);
            }

            // Section: Payments
            $w(['']);
            $w(['--- PAYMENTS ---']);
            $w(['Payment ID', 'Method', 'Status', 'Provider Ref', 'Created', 'Paid At']);
            foreach ($data->payments as $p) {
                $w([(string) $p->paymentId, $p->method, $p->status, $p->providerRef, $p->createdAtUtc, (string) ($p->paidAtUtc ?? '')]);
            }

            // Section: Tickets
            $w(['']);
            $w(['--- TICKETS ---']);
            $w(['Ticket ID', 'Code', 'Scanned', 'Scanned At', 'Scanned By']);
            foreach ($data->tickets as $t) {
                $w([(string) $t->ticketId, $t->ticketCode, $t->isScanned ? 'Yes' : 'No', (string) ($t->scannedAtUtc ?? ''), (string) ($t->scannedByUserName ?? '')]);
            }

            fclose($out);
            exit;
        });
    }

    public function exportDetailExcel(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $data = $this->ordersService->getOrderDetail($id) ?? throw new NotFoundException('Order', $id);
            $o = $data->order;

            header('Content-Type: application/vnd.ms-excel; charset=utf-8');
            header('Content-Disposition: attachment; filename=order-' . $id . '.xls');

            $e = fn(mixed $v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
            $row = fn(array $cells) => '<tr>' . implode('', array_map(fn($c) => '<td>' . $e($c) . '</td>', $cells)) . '</tr>';
            $head = fn(array $cells) => '<tr>' . implode('', array_map(fn($c) => '<th>' . $e($c) . '</th>', $cells)) . '</tr>';

            echo '<table border="1">';
            echo $head(['Order #', 'Status', 'Subtotal', 'VAT', 'Total', 'Created', 'Pay Before', 'User Email', 'Recipient', 'Recipient Email']);
            echo $row([$o->orderNumber, $o->status, $o->subtotal, $o->vatTotal, $o->totalAmount, $o->createdAtUtc, $o->payBeforeUtc, $o->userEmail, trim($o->ticketRecipientFirstName . ' ' . $o->ticketRecipientLastName), $o->ticketRecipientEmail]);

            echo $head(['Event', 'Venue', 'Session', 'Qty', 'Unit Price', 'VAT %', 'Donation', 'Line Total']);
            foreach ($data->items as $i) {
                echo $row([$i->eventTitle ?? '', $i->venueName ?? '', $i->sessionDateTime ?? '', $i->quantity, $i->unitPrice, $i->vatRate, $i->donationAmount, $i->lineTotal]);
            }

            echo $head(['Payment ID', 'Method', 'Status', 'Provider Ref', 'Created', 'Paid At']);
            foreach ($data->payments as $p) {
                echo $row([$p->paymentId, $p->method, $p->status, $p->providerRef, $p->createdAtUtc, $p->paidAtUtc ?? '']);
            }

            echo $head(['Ticket ID', 'Code', 'Scanned', 'Scanned At', 'Scanned By']);
            foreach ($data->tickets as $t) {
                echo $row([$t->ticketId, $t->ticketCode, $t->isScanned ? 'Yes' : 'No', $t->scannedAtUtc ?? '', $t->scannedByUserName ?? '']);
            }

            echo '</table>';
            exit;
        });
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
