<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * Filter criteria shared by the CMS orders list and the orders export.
 *
 * Both surfaces scope orders by an optional status and an inclusive date range
 * (matched against the order's creation date). The list paginates on top of this
 * filter; the export streams every row that matches it. Dates are 'Y-m-d' strings,
 * already validated by the controller.
 */
final readonly class CmsOrdersFilter
{
    public function __construct(
        public ?string $status,
        public string $fromDate,
        public string $toDate,
    ) {}
}
