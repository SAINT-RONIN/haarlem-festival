<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\ProgramData;

/**
 * Defines the contract for the checkout and payment processing workflow.
 */
interface ICheckoutService
{
    /**
     * Orchestrates order creation, payment record insertion, and Stripe session setup.
     *
     * @param array{firstName:string,lastName:string,email:string,paymentMethod:string,saveDetails:bool} $payload
     * @return array{redirectUrl:string,orderId:int,paymentId:int}
     */
    public function createCheckoutSession(ProgramData $programData, int $userId, array $payload): array;

    /**
     * Handles a cancelled checkout by reverting the order and payment to their pre-checkout state.
     *
     * @return array{status:string,orderId:?int,paymentId:?int}
     */
    public function handleCancel(?int $orderId, ?int $paymentId): array;

    /**
     * Processes an incoming Stripe webhook event, verifying the signature and updating order/payment status.
     *
     * @return array{processed:bool,eventId:string,eventType:string}
     */
    public function handleWebhook(string $payload, ?string $signatureHeader): array;

    /**
     * Retrieves a summary of a Stripe checkout session for the confirmation page.
     *
     * @return array{sessionId:string,paymentStatus:string,status:string,amountTotal:float,currency:string}
     */
    public function getSessionSummary(string $sessionId): array;
}

