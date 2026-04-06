<?php

declare(strict_types=1);

namespace App\DTOs\Invoice;

/**
 * Structured payload for sending invoice emails with PDF attachment.
 */
final readonly class InvoiceEmailMessage
{
    /**
     * @param object[] $attachments Each object has absolutePath and displayName properties
     */
    public function __construct(
        public string $recipientEmail,
        public string $recipientName,
        public string $orderNumber,
        public string $invoiceNumber,
        public array  $attachments,
    ) {
    }
}
