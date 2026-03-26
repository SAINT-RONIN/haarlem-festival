<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Checkout\CheckoutCancelResult;
use App\DTOs\Checkout\CheckoutSessionResult;
use App\DTOs\Checkout\CheckoutSessionSummary;
use App\DTOs\Checkout\WebhookHandlerResult;
use App\DTOs\Program\ProgramData;
use App\Models\CheckoutMainContent;

/**
 * Contract for the full checkout lifecycle: creating Stripe-backed checkout sessions,
 * handling user cancellations, processing Stripe webhook callbacks, and retrieving
 * session summaries for confirmation pages.
 */
interface ICheckoutService
{
    /**
     * Orchestrates order creation, payment record insertion, and Stripe session setup.
     *
     * @param array{firstName:string,lastName:string,email:string,paymentMethod:string,saveDetails:bool} $payload
     */
    public function createCheckoutSession(ProgramData $programData, int $userId, array $payload): CheckoutSessionResult;

    /**
     * Handles a cancelled checkout by reverting the order and payment to their pre-checkout state.
     */
    public function handleCancel(?int $orderId, ?int $paymentId): CheckoutCancelResult;

    /**
     * Processes an incoming Stripe webhook event, verifying the signature and updating order/payment status.
     */
    public function handleWebhook(string $payload, ?string $signatureHeader): WebhookHandlerResult;

    /**
     * Retrieves a summary of a Stripe checkout session for the confirmation page.
     */
    public function getSessionSummary(string $sessionId): CheckoutSessionSummary;

    /**
     * Returns the CMS content for the checkout page.
     */
    public function getCheckoutMainContent(): CheckoutMainContent;
}

