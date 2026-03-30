<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

/**
 * Generates an invoice, persists it, creates a PDF, and emails it after successful payment.
 */
interface IInvoiceFulfillmentService
{
    public function fulfillPaidOrder(int $orderId): void;
}
