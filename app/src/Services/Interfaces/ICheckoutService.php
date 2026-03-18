<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface ICheckoutService
{
    /**
     * @param array{program: mixed, items: array, subtotal: float, taxAmount: float, total: float} $programData
     * @param array{firstName:string,lastName:string,email:string,paymentMethod:string,saveDetails:bool} $payload
     * @return array{redirectUrl:string,orderId:int,paymentId:int}
     */
    public function createCheckoutSession(array $programData, int $userId, array $payload): array;

    /**
     * @return array{status:string,orderId:?int,paymentId:?int}
     */
    public function handleCancel(?int $orderId, ?int $paymentId): array;

    /**
     * @return array{processed:bool,eventId:string,eventType:string}
     */
    public function handleWebhook(string $payload, ?string $signatureHeader): array;

    /**
     * @return array<string,mixed>
     */
    public function getSessionSummary(string $sessionId): array;
}

