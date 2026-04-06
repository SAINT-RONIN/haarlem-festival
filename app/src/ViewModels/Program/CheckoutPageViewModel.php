<?php

declare(strict_types=1);

namespace App\ViewModels\Program;

/**
 * View data for the checkout page.
 *
 * Carries order summary, payment form fields, and Stripe public key.
 */
final readonly class CheckoutPageViewModel
{
    public function __construct(
        public string $pageTitle,
        public string $backButtonText,
        public string $paymentOverviewHeading,
        public string $personalInfoHeading,
        public string $personalInfoSubtext,
        public string $firstNameLabel,
        public string $firstNamePlaceholder,
        public string $lastNameLabel,
        public string $lastNamePlaceholder,
        public string $emailLabel,
        public string $emailPlaceholder,
        public string $paymentMethodsHeading,
        public string $saveDetailsLabel,
        public string $saveDetailsSubtext,
        public string $payButtonText,
        /** @var CheckoutItemViewModel[] */
        public array $items,
        public string $subtotal,
        public string $taxLabel,
        public string $taxAmount,
        public string $total,
        public bool $isLoggedIn,
        public string $checkoutJsVersion = '',
    ) {
    }
}
