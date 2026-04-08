<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Checkout;

/**
 * Typed carrier for checkout form fields submitted by the customer.
 *
 * Replaces the raw array that was previously passed to createCheckoutSession(),
 * giving each field a named property and enabling IDE autocompletion and static analysis.
 */
final readonly class CheckoutPayloadData
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $paymentMethod,
        public bool $saveDetails = false,
    ) {}

    /**
     * Creates an instance from the raw POST/JSON body array.
     *
     * Values are trimmed and cast to their expected types.
     * Returns null when the payload cannot be constructed (missing required fields).
     */
    public static function fromArray(array $body): ?self
    {
        $firstName = trim((string)($body['firstName'] ?? ''));
        $lastName  = trim((string)($body['lastName'] ?? ''));
        $email     = trim((string)($body['email'] ?? ''));
        $method    = trim((string)($body['paymentMethod'] ?? ''));
        $save      = (bool)($body['saveDetails'] ?? false);

        if ($firstName === '' || $lastName === '' || $email === '' || $method === '') {
            return null;
        }

        return new self(
            firstName: $firstName,
            lastName: $lastName,
            email: $email,
            paymentMethod: $method,
            saveDetails: $save,
        );
    }
}
