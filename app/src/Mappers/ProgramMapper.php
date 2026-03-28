<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Enums\EventTypeId;
use App\Helpers\AgeLabelFormatter;
use App\Helpers\FormatHelper;
use App\Content\CheckoutMainContent;
use App\DTOs\Program\ProgramData;
use App\DTOs\Program\ProgramItemData;
use App\Content\ProgramMainContent;
use App\ViewModels\Program\CheckoutItemViewModel;
use App\ViewModels\Program\CheckoutPageViewModel;
use App\ViewModels\Program\MyProgramPageViewModel;
use App\ViewModels\Program\ProgramItemViewModel;

/**
 * Transforms program-item domain models into ViewModels for the "My Program" page
 * and the checkout page, computing line totals, formatting prices, and resolving
 * event-type icons and language/age labels.
 */
final class ProgramMapper
{
    private const LANGUAGE_LABELS = ['NL' => 'Dutch', 'ENG' => 'English', 'ZH' => 'Chinese'];

    /**
     * Converts a single ProgramItemData into a display-ready ViewModel for the My Program list.
     * Computes the line total (price x quantity + donation) and resolves the event-type icon URL.
     */
    public static function toItemViewModel(ProgramItemData $item): ProgramItemViewModel
    {
        $lineTotal = self::lineTotal($item);
        // "Pay what you like" events with a zero base price display as "Free"
        $priceDisplay = ($item->isPayWhatYouLike && $item->basePrice <= 0.0) ? 'Free' : FormatHelper::price($item->basePrice);

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
            donationDisplay: $item->donationAmount > 0 ? FormatHelper::price($item->donationAmount) : '',
            sumDisplay: FormatHelper::price($lineTotal),
            eventTypeSlug: $item->eventTypeSlug,
            eventTypeLabel: self::getEventTypeLabel($item->eventTypeId, $item->eventTypeName),
            eventTypeImageUrl: self::getEventTypeImageUrl($item->eventTypeId),
            isPayWhatYouLike: $item->isPayWhatYouLike,
            languageLabel: self::buildLanguageLabel($item->languageCode),
            ageLabel: AgeLabelFormatter::format($item->minAge, $item->maxAge),
        );
    }

    /**
     * Converts a ProgramItemData into a compact checkout-summary ViewModel (quantity, title, line total).
     */
    public static function toCheckoutItemViewModel(ProgramItemData $item): CheckoutItemViewModel
    {
        $lineTotal = self::lineTotal($item);

        return new CheckoutItemViewModel(
            quantityDisplay: $item->quantity . '×',
            eventTitle: $item->eventTitle,
            priceDisplay: FormatHelper::price($lineTotal),
        );
    }

    /**
     * Assembles the full My Program page ViewModel from program items, CMS labels,
     * and computed subtotal/tax/total. Consumed by the my-program view.
     */
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
            subtotal: FormatHelper::price($programData->subtotal),
            taxLabel: $cmsContent->taxLabel ?? '',
            taxAmount: FormatHelper::price($programData->taxAmount),
            total: FormatHelper::price($programData->total),
            checkoutButtonText: $cmsContent->checkoutButtonText ?? '',
            canCheckout: $itemViewModels !== [],
            isLoggedIn: $isLoggedIn,
        );
    }

    /**
     * Assembles the checkout page ViewModel with personal-info form labels,
     * payment method headings, and the order summary. Consumed by the checkout view.
     */
    public static function toCheckoutViewModel(
        ProgramData $programData,
        CheckoutMainContent $cmsContent,
        bool $isLoggedIn,
        string $checkoutJsVersion = '',
    ): CheckoutPageViewModel {
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
            subtotal: FormatHelper::price($programData->subtotal),
            taxLabel: $cmsContent->taxLabel ?? '',
            taxAmount: FormatHelper::price($programData->taxAmount),
            total: FormatHelper::price($programData->total),
            isLoggedIn: $isLoggedIn,
            checkoutJsVersion: $checkoutJsVersion,
        );
    }

    private static function lineTotal(ProgramItemData $item): float
    {
        return ($item->basePrice * $item->quantity) + $item->donationAmount;
    }

    /**
     * Returns pre-formatted subtotal, tax, and total strings for AJAX cart updates.
     *
     * @return array{subtotal: string, taxAmount: string, total: string}
     */
    public static function formatTotals(ProgramData $programData): array
    {
        return [
            'subtotal' => FormatHelper::price($programData->subtotal),
            'taxAmount' => FormatHelper::price($programData->taxAmount),
            'total' => FormatHelper::price($programData->total),
        ];
    }

    /**
     * Resolves the My Program icon path for a given event type.
     * These are static design assets with no DB counterpart.
     */
    private static function getEventTypeImageUrl(int $eventTypeId): string
    {
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

    /** Converts an ISO-style language code (NL, ENG, ZH) into a human-readable label. */
    private static function buildLanguageLabel(?string $languageCode): ?string
    {
        if ($languageCode === null || $languageCode === '') {
            return null;
        }

        return self::LANGUAGE_LABELS[strtoupper($languageCode)] ?? $languageCode;
    }

}
