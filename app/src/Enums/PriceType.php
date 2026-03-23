<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Session pricing models: free, fixed price, or pay-what-you-like.
 *
 * Determines checkout behavior.
 */
enum PriceType: string
{
    case Free = 'free';
    case Fixed = 'fixed';
    case PayWhatYouLike = 'pay-what-you-like';
}
