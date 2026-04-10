<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * CMS content for the my-program page (headings, empty state text).
 * Hydrated from CMS key-value pairs.
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
}
