<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\Cms\CmsOrdersFilter;
use App\Exceptions\NotFoundException;
use App\Export\OrderExportColumns;
use App\Mappers\CmsOrdersMapper;
use App\Services\Interfaces\ICmsOrdersService;
use App\Services\Interfaces\ISessionService;
use App\Services\Interfaces\ITicketFulfillmentService;
use App\ViewModels\Cms\CmsOrderDetailViewModel;
use App\ViewModels\Cms\CmsOrdersListViewModel;

/**
 * CMS controller for viewing customer orders, exporting data, and resending ticket emails.
 */
class CmsOrdersController extends CmsBaseController
{
    private const PAGE_SIZE = 15;

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
            $viewModel = $this->buildOrdersViewModel($this->resolveFilter());
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
            $orders = $this->ordersService->getOrders($this->resolveFilter());
            $keys = OrderExportColumns::resolveKeys($this->readColumnsParam());

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=orders.csv');

            $out = fopen('php://output', 'w');
            fputcsv($out, OrderExportColumns::labels($keys), ',', '"', '');

            foreach ($orders as $o) {
                fputcsv($out, OrderExportColumns::row($o, $keys), ',', '"', '');
            }

            fclose($out);
            exit;
        });
    }

    public function exportExcel(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $orders = $this->ordersService->getOrders($this->resolveFilter());
            $keys = OrderExportColumns::resolveKeys($this->readColumnsParam());

            header('Content-Type: application/vnd.ms-excel; charset=utf-8');
            header('Content-Disposition: attachment; filename=orders.xls');

            $esc = fn(string $v) => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

            echo "<table border='1'><thead><tr>";
            foreach (OrderExportColumns::labels($keys) as $label) {
                echo '<th>' . $esc($label) . '</th>';
            }
            echo '</tr></thead><tbody>';

            foreach ($orders as $o) {
                echo '<tr>';
                foreach (OrderExportColumns::row($o, $keys) as $cell) {
                    echo '<td>' . $esc($cell) . '</td>';
                }
                echo '</tr>';
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

    /**
     * Reads the requested export columns from the query string ($_GET['columns']).
     * Returns only string values; the whitelist does the actual key validation.
     *
     * @return string[]
     */
    private function readColumnsParam(): array
    {
        $columns = $_GET['columns'] ?? [];
        if (!is_array($columns)) {
            return [];
        }

        return array_values(array_filter($columns, 'is_string'));
    }

    /** Fetches the requested page of orders for the filter and maps them to the list view model. */
    private function buildOrdersViewModel(CmsOrdersFilter $filter): CmsOrdersListViewModel
    {
        $totalCount = $this->ordersService->countOrders($filter);
        $totalPages = max(1, (int) ceil($totalCount / self::PAGE_SIZE));

        // Clamp the requested page into range so an out-of-bounds ?page never shows a blank list.
        $currentPage = min($this->readPositiveIntQueryParam('page') ?? 1, $totalPages);
        $offset = ($currentPage - 1) * self::PAGE_SIZE;

        $ordersData = $this->ordersService->getOrders($filter, self::PAGE_SIZE, $offset);

        return CmsOrdersMapper::toListViewModel(
            $ordersData,
            $filter,
            $currentPage,
            $totalPages,
            $totalCount,
            $this->sessionService->consumeFlash('success'),
            $this->sessionService->consumeFlash('error'),
        );
    }

    /**
     * Builds the orders filter from the query string. The status is optional; the
     * date range defaults to the current week (Mon–Sun) when not supplied or invalid.
     * Shared by the list view and both list exports so they always stay in sync.
     */
    private function resolveFilter(): CmsOrdersFilter
    {
        [$fromDate, $toDate] = $this->resolveDateRange();

        return new CmsOrdersFilter(
            status: $this->readStringQueryParam('status'),
            fromDate: $fromDate,
            toDate: $toDate,
        );
    }

    /**
     * Resolves the [from, to] date range ('Y-m-d') from the query string, falling
     * back to the current week for missing/invalid values and swapping a reversed
     * range so 'from' is never after 'to'.
     *
     * @return array{0: string, 1: string}
     */
    private function resolveDateRange(): array
    {
        $fromDate = $this->normalizeDate($this->readStringQueryParam('from')) ?? self::currentWeekStart();
        $toDate = $this->normalizeDate($this->readStringQueryParam('to')) ?? self::currentWeekEnd();

        if ($fromDate > $toDate) {
            return [$toDate, $fromDate];
        }

        return [$fromDate, $toDate];
    }

    /** Validates a 'Y-m-d' date string, returning the normalized value or null if invalid. */
    private function normalizeDate(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        if ($date === false || $date->format('Y-m-d') !== $value) {
            return null;
        }

        return $value;
    }

    /** Monday of the week containing today ('Y-m-d'). */
    private static function currentWeekStart(): string
    {
        return (new \DateTimeImmutable('monday this week'))->format('Y-m-d');
    }

    /** Sunday of the week containing today ('Y-m-d'). */
    private static function currentWeekEnd(): string
    {
        return (new \DateTimeImmutable('sunday this week'))->format('Y-m-d');
    }

    /** Fetches a single order's detail data and maps it to the detail view model. */
    private function buildOrderDetailViewModel(int $orderId): CmsOrderDetailViewModel
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
