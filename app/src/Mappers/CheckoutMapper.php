<?php

declare(strict_types=1);

namespace App\Mappers;

use App\ViewModels\Program\CheckoutCancelPageViewModel;
use App\ViewModels\Program\CheckoutSuccessPageViewModel;

final class CheckoutMapper
{
    /**
     * @param array{sessionId:string,paymentStatus:string,status:string,amountTotal:float,currency:string}|null $sessionSummary
     */
    public static function toSuccessViewModel(?array $sessionSummary, bool $isLoggedIn): CheckoutSuccessPageViewModel
    {
        return new CheckoutSuccessPageViewModel(
            isLoggedIn: $isLoggedIn,
            hasSessionData: $sessionSummary !== null,
            sessionId: (string)($sessionSummary['sessionId'] ?? ''),
            paymentStatus: (string)($sessionSummary['paymentStatus'] ?? 'unknown'),
            checkoutStatus: (string)($sessionSummary['status'] ?? 'unknown'),
        );
    }

    /**
     * @param array{status:string,orderId:?int,paymentId:?int} $cancelResult
     */
    public static function toCancelViewModel(array $cancelResult, bool $isLoggedIn): CheckoutCancelPageViewModel
    {
        return new CheckoutCancelPageViewModel(
            isLoggedIn: $isLoggedIn,
            hasCancelData: $cancelResult !== [],
            orderId: (string)($cancelResult['orderId'] ?? 'n/a'),
            paymentId: (string)($cancelResult['paymentId'] ?? 'n/a'),
        );
    }
}
