<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries the CMS item values for the checkout page main section.
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

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            pageTitle: $raw['page_title'] ?? null,
            backButtonText: $raw['back_button_text'] ?? null,
            paymentOverviewHeading: $raw['payment_overview_heading'] ?? null,
            personalInfoHeading: $raw['personal_info_heading'] ?? null,
            personalInfoSubtext: $raw['personal_info_subtext'] ?? null,
            firstNameLabel: $raw['first_name_label'] ?? null,
            firstNamePlaceholder: $raw['first_name_placeholder'] ?? null,
            lastNameLabel: $raw['last_name_label'] ?? null,
            lastNamePlaceholder: $raw['last_name_placeholder'] ?? null,
            emailLabel: $raw['email_label'] ?? null,
            emailPlaceholder: $raw['email_placeholder'] ?? null,
            paymentMethodsHeading: $raw['payment_methods_heading'] ?? null,
            saveDetailsLabel: $raw['save_details_label'] ?? null,
            saveDetailsSubtext: $raw['save_details_subtext'] ?? null,
            payButtonText: $raw['pay_button_text'] ?? null,
            taxLabel: $raw['tax_label'] ?? null,
        );
    }
}
