<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\OrderHistory\OrderSummaryDto;
use App\DTOs\OrderHistory\TicketPdfDto;
use App\Helpers\FormatHelper;
use App\ViewModels\Program\MyOrdersItemViewModel;
use App\ViewModels\Program\MyOrdersViewModel;

/**
 * Transforms order history DTOs into display-ready ViewModels for the "My Orders" page.
 * Handles status badge resolution, date formatting, and ticket PDF URL assembly.
 */
final class OrderHistoryMapper
{
    /**
     * Assembles the full "My Orders" page ViewModel from order summaries and ticket data.
     *
     * @param OrderSummaryDto[] $orderDtos
     * @param array<int, TicketPdfDto[]> $ticketsByOrder Keyed by orderId.
     */
    public static function toMyOrdersViewModel(
        array $orderDtos,
        array $ticketsByOrder,
        bool $isLoggedIn,
    ): MyOrdersViewModel {
        $orderViewModels = array_map(
            fn(OrderSummaryDto $dto) => self::toOrderItemViewModel(
                $dto,
                $ticketsByOrder[$dto->orderId] ?? [],
            ),
            $orderDtos,
        );

        return new MyOrdersViewModel(
            orders: $orderViewModels,
            isLoggedIn: $isLoggedIn,
        );
    }

    /**
     * Converts a single order summary DTO into a display-ready card ViewModel.
     *
     * @param TicketPdfDto[] $ticketPdfs
     */
    public static function toOrderItemViewModel(
        OrderSummaryDto $dto,
        array $ticketPdfs,
    ): MyOrdersItemViewModel {
        $ticketPdfUrls = array_map(
            fn(TicketPdfDto $pdf) => [
                'ticketCode' => $pdf->ticketCode,
                'url' => $pdf->filePath,
            ],
            $ticketPdfs,
        );

        return new MyOrdersItemViewModel(
            orderId: $dto->orderId,
            orderNumber: $dto->orderNumber,
            statusText: $dto->status,
            statusBadgeClass: self::resolveStatusBadgeClass($dto->status),
            totalAmountFormatted: FormatHelper::price((float) $dto->totalAmount),
            createdAtFormatted: self::formatDate($dto->createdAtUtc),
            itemCount: $dto->itemCount,
            paymentStatusText: $dto->paymentStatus ?? 'N/A',
            paymentBadgeClass: self::resolvePaymentBadgeClass($dto->paymentStatus),
            ticketPdfUrls: $ticketPdfUrls,
            canRetryPayment: self::canRetryPayment($dto),
            retryUrl: '/checkout/retry/' . $dto->orderId,
        );
    }

    /** Maps an order status string to a Tailwind badge class. */
    private static function resolveStatusBadgeClass(string $status): string
    {
        return match ($status) {
            'Paid' => 'bg-green-100 text-green-800',
            'Pending' => 'bg-yellow-100 text-yellow-800',
            'Cancelled' => 'bg-red-100 text-red-800',
            'Expired' => 'bg-gray-100 text-gray-800',
            'Refunded' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /** Maps a payment status string to a Tailwind badge class. */
    private static function resolvePaymentBadgeClass(?string $status): string
    {
        if ($status === null) {
            return 'bg-gray-100 text-gray-800';
        }

        return match ($status) {
            'Paid', 'Completed', 'succeeded' => 'bg-green-100 text-green-800',
            'Pending', 'pending' => 'bg-yellow-100 text-yellow-800',
            'Cancelled', 'canceled' => 'bg-red-100 text-red-800',
            'Expired', 'expired' => 'bg-gray-100 text-gray-800',
            'Refunded', 'refunded' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /** Determines whether a pending order is still eligible for a retry payment attempt. */
    private static function canRetryPayment(OrderSummaryDto $dto): bool
    {
        if ($dto->status !== 'Pending') {
            return false;
        }

        if ($dto->payBeforeUtc === null) {
            return false;
        }

        $deadline = new \DateTimeImmutable($dto->payBeforeUtc);
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        return $deadline > $now;
    }

    /** Formats a UTC datetime string into a user-friendly display format. */
    private static function formatDate(string $dateTimeUtc): string
    {
        $date = new \DateTimeImmutable($dateTimeUtc);

        return $date->format('d M Y, H:i');
    }
}
