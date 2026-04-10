<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * CMS content for the checkout page (headings, labels, terms text).
 * Hydrated from CMS key-value pairs.
 */
final readonly class CheckoutMainContent
{
    public function __construct(
        public ?string $pageTitle,
        public ?string $backButtonText,
        public ?string $paymentOverviewHeading,
        public ?string $personalInfoHeading,
        public ?string $personalInfoSubtext,
        public ?string $firstNameLabel,
        public ?string $firstNamePlaceholder,
        public ?string $lastNameLabel,
        public ?string $lastNamePlaceholder,
        public ?string $emailLabel,
        public ?string $emailPlaceholder,
        public ?string $paymentMethodsHeading,
        public ?string $saveDetailsLabel,
        public ?string $saveDetailsSubtext,
        public ?string $payButtonText,
        public ?string $taxLabel,
    ) {}
}
