<?php

declare(strict_types=1);

namespace App\ViewModels\Program;

/**
 * View data for the my-program (cart) page.
 *
 * Carries line items, totals, checkout eligibility, and CMS content.
 */
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
    ) {}
}
