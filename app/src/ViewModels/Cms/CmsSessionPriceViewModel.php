<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * A single price row in the session pricing table.
 *
 * Includes resolved tier name for display.
 */
final readonly class CmsSessionPriceViewModel
{
    public function __construct(
        public int    $priceTierId,
        public string $tierName,
        public string $price,
        public string $currencyCode,
    ) {}
}
