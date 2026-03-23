<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * ViewModel for displaying a session price with its resolved tier name.
 */
final readonly class CmsSessionPriceViewModel
{
    public function __construct(
        public int    $priceTierId,
        public string $tierName,
        public string $price,
        public string $currencyCode,
    ) {
    }
}
