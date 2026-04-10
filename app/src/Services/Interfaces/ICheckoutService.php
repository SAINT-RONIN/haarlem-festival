<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Domain\Checkout\CheckoutCancelResult;
use App\DTOs\Domain\Checkout\CheckoutPayloadData;
use App\DTOs\Domain\Checkout\CheckoutSessionResult;
use App\DTOs\Domain\Checkout\CheckoutSessionSummary;
use App\DTOs\Domain\Program\ProgramData;
use App\DTOs\Cms\CheckoutMainContent;

/**
 * Contract for the checkout lifecycle: creating Stripe-backed checkout sessions,
 * handling user cancellations, and retrieving session summaries for confirmation pages.
 *
 * Webhook processing is handled separately by IStripeWebhookHandler, with
 * the success-page return path acting as a paid-session fallback in local/dev.
 */
interface ICheckoutService
{
    /**
     * Orchestrates order creation, payment record insertion, and Stripe session setup.
     */
    public function createCheckoutSession(ProgramData $programData, int $userId, CheckoutPayloadData $payload): CheckoutSessionResult;

    /**
     * Handles a cancelled checkout by reverting the order and payment to their pre-checkout state.
     */
    public function handleCancel(?int $orderId, ?int $paymentId): CheckoutCancelResult;

    /**
     * Loads and validates an order for retry payment.
     *
     * @throws \App\Exceptions\RetryPaymentException When order not found or not owned by user.
     */
    public function getRetryOrder(int $orderId, int $userId): \App\Models\Order;

    /**
     * Creates a new Stripe session for an existing pending order within the 24h payment window.
     *
     * @param array{paymentMethod:string} $payload
     * @throws \App\Exceptions\RetryPaymentException When order is not eligible for retry.
     */
    public function retryCheckoutSession(int $orderId, int $userId, array $payload): CheckoutSessionResult;

    /**
     * Retrieves a summary of a Stripe checkout session for the confirmation page.
     */
    public function getSessionSummary(string $sessionId): CheckoutSessionSummary;

    /**
     * Returns the CMS content for the checkout page.
     */
    public function getCheckoutMainContent(): CheckoutMainContent;
}
