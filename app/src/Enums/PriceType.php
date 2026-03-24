<?php

declare(strict_types=1);

namespace App\Enums;

enum PriceType: string
{
    case Free = 'free';
    case Fixed = 'fixed';
    case PayWhatYouLike = 'pay-what-you-like';
}
