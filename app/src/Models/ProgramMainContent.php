<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries the CMS item values for the my-program page main section.
 */
final readonly class ProgramMainContent
{
    public function __construct(
        public ?string $pageTitle,
        public ?string $selectedEventsHeading,
        public ?string $payWhatYouLikeMessage,
        public ?string $clearButtonText,
        public ?string $continueExploringText,
        public ?string $paymentOverviewHeading,
        public ?string $taxLabel,
        public ?string $checkoutButtonText,
    ) {}

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            pageTitle: $raw['page_title'] ?? null,
            selectedEventsHeading: $raw['selected_events_heading'] ?? null,
            payWhatYouLikeMessage: $raw['pay_what_you_like_message'] ?? null,
            clearButtonText: $raw['clear_button_text'] ?? null,
            continueExploringText: $raw['continue_exploring_text'] ?? null,
            paymentOverviewHeading: $raw['payment_overview_heading'] ?? null,
            taxLabel: $raw['tax_label'] ?? null,
            checkoutButtonText: $raw['checkout_button_text'] ?? null,
        );
    }
}
