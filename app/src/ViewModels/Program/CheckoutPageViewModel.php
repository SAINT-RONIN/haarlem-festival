<?php

declare(strict_types=1);

namespace App\ViewModels\Program;

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
    ) {
    }

    /**
     * @param array{program: ?\App\Models\Program, items: array<int, array<string, mixed>>, subtotal: float, taxAmount: float, total: float} $programData
     * @param array<string, string> $cmsContent
     */
    public static function fromServiceData(array $programData, array $cmsContent, bool $isLoggedIn): self
    {
        $itemViewModels = array_map(
            [CheckoutItemViewModel::class, 'fromItemData'],
            $programData['items']
        );

        return new self(
            pageTitle: $cmsContent['page_title'] ?? 'Checkout',
            backButtonText: $cmsContent['back_button_text'] ?? 'Back to my program',
            paymentOverviewHeading: $cmsContent['payment_overview_heading'] ?? 'PAYMENT OVERVIEW',
            personalInfoHeading: $cmsContent['personal_info_heading'] ?? 'Personal information',
            personalInfoSubtext: $cmsContent['personal_info_subtext'] ?? 'We need this information to send your tickets to you.',
            firstNameLabel: $cmsContent['first_name_label'] ?? 'First name',
            firstNamePlaceholder: $cmsContent['first_name_placeholder'] ?? 'Enter your first name',
            lastNameLabel: $cmsContent['last_name_label'] ?? 'Last name',
            lastNamePlaceholder: $cmsContent['last_name_placeholder'] ?? 'Enter your last name',
            emailLabel: $cmsContent['email_label'] ?? 'Email address',
            emailPlaceholder: $cmsContent['email_placeholder'] ?? 'Enter your email address',
            paymentMethodsHeading: $cmsContent['payment_methods_heading'] ?? 'PAYMENT METHODS',
            saveDetailsLabel: $cmsContent['save_details_label'] ?? 'Save your details for faster checkout',
            saveDetailsSubtext: $cmsContent['save_details_subtext'] ?? 'Next time you visit, you will not need to enter your details again.',
            payButtonText: $cmsContent['pay_button_text'] ?? 'Pay',
            items: $itemViewModels,
            subtotal: ProgramItemViewModel::formatPrice((float)$programData['subtotal']),
            taxLabel: $cmsContent['tax_label'] ?? 'Tax (21% VAT)',
            taxAmount: ProgramItemViewModel::formatPrice((float)$programData['taxAmount']),
            total: ProgramItemViewModel::formatPrice((float)$programData['total']),
            isLoggedIn: $isLoggedIn,
        );
    }
}
