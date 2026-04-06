<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Domain\Checkout\CheckoutCancelResult;
use App\DTOs\Domain\Checkout\CheckoutSessionSummary;
use App\Helpers\FormatHelper;
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
            orderReference: self::resolveOrderReference($sessionSummary),
            totalLabel: self::formatTotalLabel($sessionSummary),
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

    private static function resolveOrderReference(?CheckoutSessionSummary $sessionSummary): ?string
    {
        if ($sessionSummary === null || $sessionSummary->orderReference === '') {
            return null;
        }

        return $sessionSummary->orderReference;
    }

    private static function formatTotalLabel(?CheckoutSessionSummary $sessionSummary): ?string
    {
        if ($sessionSummary === null) {
            return null;
        }

        return $sessionSummary->currency === 'EUR'
            ? FormatHelper::price($sessionSummary->amountTotal)
            : $sessionSummary->currency . ' ' . number_format($sessionSummary->amountTotal, 2, '.', '');
    }
}
