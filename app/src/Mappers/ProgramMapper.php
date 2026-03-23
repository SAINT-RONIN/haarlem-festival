<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Enums\EventTypeId;
use App\Helpers\AgeLabelFormatter;
use App\Helpers\FormatHelper;
use App\Models\CheckoutMainContent;
use App\Models\ProgramData;
use App\Models\ProgramItemData;
use App\Models\ProgramMainContent;
use App\ViewModels\Program\CheckoutItemViewModel;
use App\ViewModels\Program\CheckoutPageViewModel;
use App\ViewModels\Program\MyProgramPageViewModel;
use App\ViewModels\Program\ProgramItemViewModel;

final class ProgramMapper
{
    public static function toItemViewModel(ProgramItemData $item): ProgramItemViewModel
    {
        $lineTotal = ($item->basePrice * $item->quantity) + $item->donationAmount;
        $priceDisplay = ($item->isPayWhatYouLike && $item->basePrice <= 0.0) ? 'Free' : self::formatPrice($item->basePrice);

        return new ProgramItemViewModel(
            programItemId: $item->programItemId,
            eventSessionId: $item->eventSessionId,
            eventTitle: $item->eventTitle,
            locationDisplay: self::buildLocationDisplay($item->venueName ?? '', $item->hallName),
            dateTimeDisplay: self::buildDateTimeDisplay($item->startDateTime, $item->endDateTime),
            priceDisplay: $priceDisplay,
            rawPrice: $item->basePrice,
            quantity: $item->quantity,
            donationAmount: $item->donationAmount,
            donationDisplay: $item->donationAmount > 0 ? self::formatPrice($item->donationAmount) : '',
            sumDisplay: self::formatPrice($lineTotal),
            eventTypeSlug: $item->eventTypeSlug,
            eventTypeLabel: self::getEventTypeLabel($item->eventTypeId, $item->eventTypeName),
            eventTypeImageUrl: self::getEventTypeImageUrl($item->eventTypeId),
            isPayWhatYouLike: $item->isPayWhatYouLike,
            languageLabel: self::buildLanguageLabel($item->languageCode),
            ageLabel: AgeLabelFormatter::format($item->minAge, $item->maxAge),
        );
    }

    public static function toCheckoutItemViewModel(ProgramItemData $item): CheckoutItemViewModel
    {
        $lineTotal = ($item->basePrice * $item->quantity) + $item->donationAmount;

        return new CheckoutItemViewModel(
            quantityDisplay: $item->quantity . '×',
            eventTitle: $item->eventTitle,
            priceDisplay: self::formatPrice($lineTotal),
        );
    }

    public static function toMyProgramViewModel(ProgramData $programData, ProgramMainContent $cmsContent, bool $isLoggedIn): MyProgramPageViewModel
    {
        $itemViewModels = array_map([self::class, 'toItemViewModel'], $programData->items);

        return new MyProgramPageViewModel(
            pageTitle: $cmsContent->pageTitle ?? '',
            selectedEventsHeading: $cmsContent->selectedEventsHeading ?? '',
            payWhatYouLikeMessage: $cmsContent->payWhatYouLikeMessage ?? '',
            clearButtonText: $cmsContent->clearButtonText ?? '',
            continueExploringText: $cmsContent->continueExploringText ?? '',
            paymentOverviewHeading: $cmsContent->paymentOverviewHeading ?? '',
            items: $itemViewModels,
            subtotal: self::formatPrice($programData->subtotal),
            taxLabel: $cmsContent->taxLabel ?? '',
            taxAmount: self::formatPrice($programData->taxAmount),
            total: self::formatPrice($programData->total),
            checkoutButtonText: $cmsContent->checkoutButtonText ?? '',
            canCheckout: $itemViewModels !== [],
            isLoggedIn: $isLoggedIn,
        );
    }

    public static function toCheckoutViewModel(ProgramData $programData, CheckoutMainContent $cmsContent, bool $isLoggedIn): CheckoutPageViewModel
    {
        $itemViewModels = array_map([self::class, 'toCheckoutItemViewModel'], $programData->items);

        return new CheckoutPageViewModel(
            pageTitle: $cmsContent->pageTitle ?? '',
            backButtonText: $cmsContent->backButtonText ?? '',
            paymentOverviewHeading: $cmsContent->paymentOverviewHeading ?? '',
            personalInfoHeading: $cmsContent->personalInfoHeading ?? '',
            personalInfoSubtext: $cmsContent->personalInfoSubtext ?? '',
            firstNameLabel: $cmsContent->firstNameLabel ?? '',
            firstNamePlaceholder: $cmsContent->firstNamePlaceholder ?? '',
            lastNameLabel: $cmsContent->lastNameLabel ?? '',
            lastNamePlaceholder: $cmsContent->lastNamePlaceholder ?? '',
            emailLabel: $cmsContent->emailLabel ?? '',
            emailPlaceholder: $cmsContent->emailPlaceholder ?? '',
            paymentMethodsHeading: $cmsContent->paymentMethodsHeading ?? '',
            saveDetailsLabel: $cmsContent->saveDetailsLabel ?? '',
            saveDetailsSubtext: $cmsContent->saveDetailsSubtext ?? '',
            payButtonText: $cmsContent->payButtonText ?? '',
            items: $itemViewModels,
            subtotal: self::formatPrice($programData->subtotal),
            taxLabel: $cmsContent->taxLabel ?? '',
            taxAmount: self::formatPrice($programData->taxAmount),
            total: self::formatPrice($programData->total),
            isLoggedIn: $isLoggedIn,
        );
    }

    public static function formatPrice(float $amount): string
    {
        return FormatHelper::price($amount);
    }

    /**
     * @return array{subtotal: string, taxAmount: string, total: string}
     */
    public static function formatTotals(ProgramData $programData): array
    {
        return [
            'subtotal' => self::formatPrice($programData->subtotal),
            'taxAmount' => self::formatPrice($programData->taxAmount),
            'total' => self::formatPrice($programData->total),
        ];
    }

    private static function getEventTypeImageUrl(int $eventTypeId): string
    {
        // Maps event type IDs to static icon filenames in assets/icons/my-program/.
        // These are design assets, not user-facing text — no DB column exists for them.
        $filename = match ($eventTypeId) {
            EventTypeId::Jazz->value => 'saxophone',
            EventTypeId::Dance->value => 'dance',
            EventTypeId::History->value => 'museum',
            EventTypeId::Storytelling->value => 'book',
            EventTypeId::Restaurant->value => 'food-meal',
            default => 'book',
        };

        return "/assets/icons/my-program/{$filename}.png";
    }

    private static function getEventTypeLabel(int $eventTypeId, string $fallback): string
    {
        return $fallback !== '' ? $fallback : (string)$eventTypeId;
    }

    private static function buildLocationDisplay(string $venueName, ?string $hallName): string
    {
        if ($venueName === '') {
            return '';
        }

        if ($hallName !== null && $hallName !== '') {
            return "{$venueName} - {$hallName}";
        }

        return $venueName;
    }

    private static function buildDateTimeDisplay(string $startDateTime, ?string $endDateTime): string
    {
        if ($startDateTime === '') {
            return '';
        }

        $start = new \DateTimeImmutable($startDateTime);
        $dayAndDate = $start->format('l, F j');
        $startTime = $start->format('H:i');

        if ($endDateTime !== null && $endDateTime !== '') {
            $end = new \DateTimeImmutable($endDateTime);
            $endTime = $end->format('H:i');
            return "{$dayAndDate} · {$startTime} - {$endTime}";
        }

        return "{$dayAndDate} · {$startTime}";
    }

    private static function buildLanguageLabel(?string $languageCode): ?string
    {
        if ($languageCode === null || $languageCode === '') {
            return null;
        }

        $labels = [
            'NL' => 'Dutch',
            'ENG' => 'English',
            'ZH' => 'Chinese',
        ];

        return $labels[strtoupper($languageCode)] ?? $languageCode;
    }
}
