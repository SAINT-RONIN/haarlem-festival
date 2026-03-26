<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Checkout\CheckoutCancelResult;
use App\DTOs\Checkout\CheckoutSessionSummary;
use App\ViewModels\Program\CheckoutCancelPageViewModel;
use App\ViewModels\Program\CheckoutSuccessPageViewModel;

/**
 * Transforms Stripe/payment session results into ViewModels for the checkout
 * success and cancel confirmation pages.
 */
final class CheckoutMapper
{
    /**
     * Builds the checkout-success page ViewModel from the Stripe session summary,
     * gracefully handling a null summary (e.g. when the session has already expired).
     */
    public static function toSuccessViewModel(?CheckoutSessionSummary $sessionSummary, bool $isLoggedIn): CheckoutSuccessPageViewModel
    {
        return new CheckoutSuccessPageViewModel(
            isLoggedIn: $isLoggedIn,
            hasSessionData: $sessionSummary !== null,
            sessionId: $sessionSummary?->sessionId ?? '',
            paymentStatus: $sessionSummary?->paymentStatus ?? 'unknown',
            checkoutStatus: $sessionSummary?->status ?? 'unknown',
        );
    }

    /**
     * Builds the checkout-cancel page ViewModel, displaying the cancelled order/payment IDs
     * so the user knows which transaction was abandoned.
     */
    public static function toCancelViewModel(CheckoutCancelResult $cancelResult, bool $isLoggedIn): CheckoutCancelPageViewModel
    {
        return new CheckoutCancelPageViewModel(
            isLoggedIn: $isLoggedIn,
            hasCancelData: true,
            orderId: (string)($cancelResult->orderId ?? 'n/a'),
            paymentId: (string)($cancelResult->paymentId ?? 'n/a'),
        );
    }

    /**
     * Builds a single consolidated Stripe line item for the full order total.
     *
     * @return array<int, array{price_data: array{currency: string, unit_amount: int, product_data: array{name: string}}, quantity: int}>
     */
    public static function buildStripeLineItems(float $total, string $orderNumber): array
    {
        return [[
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => (int)round($total * 100),
                'product_data' => ['name' => 'Haarlem Festival order ' . $orderNumber],
            ],
            'quantity' => 1,
        ]];
    }
}
