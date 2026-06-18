<?php

declare(strict_types=1);

namespace App\Export;

use App\DTOs\Domain\Checkout\OrderWithDetails;

/**
 * Whitelist + value catalog for the CMS orders list export.
 *
 * Each entry maps a stable column key to a human label and a small function
 * that reads one value out of an order. This single catalog drives the CSV
 * header/rows, the Excel header/rows, and the column checkboxes on the orders
 * page, so all three always stay in sync.
 *
 * Amounts: the database does not store a paid amount, so "paid" is worked out
 * from the order status (see calculatePaidAmount / calculateOutstandingAmount).
 */
final class OrderExportColumns
{
    /**
     * @return array<string, array{label: string, extract: callable(OrderWithDetails): string}>
     */
    public static function catalog(): array
    {
        return [
            'orderNumber'       => ['label' => 'Order #',            'extract' => fn(OrderWithDetails $order): string => $order->orderNumber],
            'userAccountId'     => ['label' => 'User ID',            'extract' => fn(OrderWithDetails $order): string => (string) $order->userAccountId],
            'email'             => ['label' => 'Email',              'extract' => fn(OrderWithDetails $order): string => $order->email],
            'itemsSummary'      => ['label' => 'Items',              'extract' => fn(OrderWithDetails $order): string => $order->itemsSummary ?? ''],
            'totalAmount'       => ['label' => 'Total Amount',       'extract' => fn(OrderWithDetails $order): string => $order->totalAmount],
            'paidAmount'        => ['label' => 'Paid Amount',        'extract' => fn(OrderWithDetails $order): string => self::calculatePaidAmount($order)],
            'outstandingAmount' => ['label' => 'Outstanding Amount', 'extract' => fn(OrderWithDetails $order): string => self::calculateOutstandingAmount($order)],
            'status'            => ['label' => 'Order Status',       'extract' => fn(OrderWithDetails $order): string => $order->status],
            'paymentStatus'     => ['label' => 'Payment Status',     'extract' => fn(OrderWithDetails $order): string => $order->paymentStatus ?? 'No payment'],
            'createdAtUtc'      => ['label' => 'Date',               'extract' => fn(OrderWithDetails $order): string => $order->createdAtUtc],
        ];
    }

    /**
     * Works out how much of the order has been paid.
     *
     * There is no paid-amount column in the database, so we decide it from the
     * order status: a fully paid order counts its whole total as paid, and any
     * other status (pending, cancelled, ...) counts as nothing paid yet.
     */
    private static function calculatePaidAmount(OrderWithDetails $order): string
    {
        if ($order->status === 'Paid') {
            return $order->totalAmount;
        }

        return '0.00';
    }

    /**
     * Works out how much of the order is still owed: the total minus what was paid.
     */
    private static function calculateOutstandingAmount(OrderWithDetails $order): string
    {
        $total = (float) $order->totalAmount;
        $paid = (float) self::calculatePaidAmount($order);
        $outstanding = $total - $paid;

        return number_format($outstanding, 2, '.', '');
    }

    /** @return string[] All known column keys, in catalog order. */
    public static function allKeys(): array
    {
        return array_keys(self::catalog());
    }

    /**
     * Keeps only the requested keys that we actually know about, in catalog
     * order. If nothing valid was requested, every column is used instead.
     *
     * @param string[] $requested
     * @return string[]
     */
    public static function resolveKeys(array $requested): array
    {
        $resolvedKeys = [];

        foreach (self::allKeys() as $knownKey) {
            if (in_array($knownKey, $requested, true)) {
                $resolvedKeys[] = $knownKey;
            }
        }

        if ($resolvedKeys === []) {
            return self::allKeys();
        }

        return $resolvedKeys;
    }

    /**
     * @param string[] $keys
     * @return string[] Header labels for the given keys, in order.
     */
    public static function labels(array $keys): array
    {
        $catalog = self::catalog();
        $labels = [];

        foreach ($keys as $key) {
            $labels[] = $catalog[$key]['label'];
        }

        return $labels;
    }

    /**
     * @param string[] $keys
     * @return string[] One value per key, in the given order.
     */
    public static function row(OrderWithDetails $order, array $keys): array
    {
        $catalog = self::catalog();
        $cells = [];

        foreach ($keys as $key) {
            $extractor = $catalog[$key]['extract'];
            $cells[] = $extractor($order);
        }

        return $cells;
    }
}