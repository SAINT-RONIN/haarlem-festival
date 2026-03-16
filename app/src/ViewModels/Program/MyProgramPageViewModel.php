<?php

declare(strict_types=1);

namespace App\ViewModels\Program;

final readonly class MyProgramPageViewModel
{
    public function __construct(
        public string $pageTitle,
        public string $selectedEventsHeading,
        public string $payWhatYouLikeMessage,
        public string $clearButtonText,
        public string $continueExploringText,
        public string $paymentOverviewHeading,
        /** @var ProgramItemViewModel[] */
        public array $items,
        public string $subtotal,
        public string $taxLabel,
        public string $taxAmount,
        public string $total,
        public string $checkoutButtonText,
        public bool $canCheckout,
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
            [ProgramItemViewModel::class, 'fromItemData'],
            $programData['items']
        );

        return new self(
            pageTitle: $cmsContent['page_title'] ?? 'My Program',
            selectedEventsHeading: $cmsContent['selected_events_heading'] ?? 'Selected Events',
            payWhatYouLikeMessage: $cmsContent['pay_what_you_like_message'] ?? 'Choose the amount you want to pay for this story. Any contribution is welcome and supports the initiative sharing their story. You can adjust the amount before confirming your reservation.',
            clearButtonText: $cmsContent['clear_button_text'] ?? 'Clear all',
            continueExploringText: $cmsContent['continue_exploring_text'] ?? 'Continue exploring',
            paymentOverviewHeading: $cmsContent['payment_overview_heading'] ?? 'Payment Overview',
            items: $itemViewModels,
            subtotal: ProgramItemViewModel::formatPrice((float)$programData['subtotal']),
            taxLabel: $cmsContent['tax_label'] ?? 'Tax (21% VAT)',
            taxAmount: ProgramItemViewModel::formatPrice((float)$programData['taxAmount']),
            total: ProgramItemViewModel::formatPrice((float)$programData['total']),
            checkoutButtonText: $cmsContent['checkout_button_text'] ?? 'Proceed to checkout',
            canCheckout: $itemViewModels !== [],
            isLoggedIn: $isLoggedIn,
        );
    }
}
