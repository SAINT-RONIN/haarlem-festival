<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * DTO for booking call-to-action section.
 */
final readonly class BookingCallToActionData
{
    public function __construct(
        public string $headingText,
        public string $descriptionText,
    ) {
    }
}

