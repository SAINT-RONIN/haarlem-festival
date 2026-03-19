<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Enums\EventTypeId;
use App\Helpers\AgeLabelFormatter;
use App\ViewModels\Program\CheckoutItemViewModel;
use App\ViewModels\Program\CheckoutPageViewModel;
use App\ViewModels\Program\MyProgramPageViewModel;
use App\ViewModels\Program\ProgramItemViewModel;

class ProgramMapper
{
    /**
     * @param array<string, mixed> $item
     */
    public static function toItemViewModel(array $item): ProgramItemViewModel
    {
        $basePrice = (float)$item['basePrice'];
        $quantity = (int)$item['quantity'];
        $donationAmount = (float)$item['donationAmount'];
        $eventTypeId = (int)$item['eventTypeId'];
        $eventTypeSlug = (string)$item['eventTypeSlug'];
        $isPayWhatYouLike = (bool)$item['isPayWhatYouLike'];

        $lineTotal = ($basePrice * $quantity) + $donationAmount;
        $priceDisplay = ($isPayWhatYouLike && $basePrice <= 0.0) ? 'Free' : self::formatPrice($basePrice);

        return new ProgramItemViewModel(
            programItemId: (int)$item['programItemId'],
            eventSessionId: (int)$item['eventSessionId'],
            eventTitle: (string)$item['eventTitle'],
            locationDisplay: self::buildLocationDisplay($item['venueName'] ?? '', $item['hallName'] ?? null),
            dateTimeDisplay: self::buildDateTimeDisplay($item['startDateTime'] ?? '', $item['endDateTime'] ?? null),
            priceDisplay: $priceDisplay,
            rawPrice: $basePrice,
            quantity: $quantity,
            donationAmount: $donationAmount,
            donationDisplay: $donationAmount > 0 ? self::formatPrice($donationAmount) : '',
            sumDisplay: self::formatPrice($lineTotal),
            eventTypeSlug: $eventTypeSlug,
            eventTypeLabel: self::getEventTypeLabel($eventTypeId, (string)$item['eventTypeName']),
            eventTypeImageUrl: self::getEventTypeImageUrl($eventTypeId),
            isPayWhatYouLike: $isPayWhatYouLike,
            languageLabel: self::buildLanguageLabel($item['languageCode'] ?? null),
            ageLabel: AgeLabelFormatter::format($item['minAge'] ?? null, $item['maxAge'] ?? null),
        );
    }

    /**
     * @param array<string, mixed> $item
     */
    public static function toCheckoutItemViewModel(array $item): CheckoutItemViewModel
    {
        $quantity = (int)$item['quantity'];
        $basePrice = (float)$item['basePrice'];
        $donationAmount = (float)$item['donationAmount'];
        $lineTotal = ($basePrice * $quantity) + $donationAmount;

        return new CheckoutItemViewModel(
            quantityDisplay: $quantity . '×',
            eventTitle: (string)$item['eventTitle'],
            priceDisplay: self::formatPrice($lineTotal),
        );
    }

    /**
     * @param array{program: ?\App\Models\Program, items: array<int, array<string, mixed>>, subtotal: float, taxAmount: float, total: float} $programData
     * @param array<string, string> $cmsContent
     */
    public static function toMyProgramViewModel(array $programData, array $cmsContent, bool $isLoggedIn): MyProgramPageViewModel
    {
        $itemViewModels = array_map([self::class, 'toItemViewModel'], $programData['items']);

        return new MyProgramPageViewModel(
            pageTitle: $cmsContent['page_title'] ?? '',
            selectedEventsHeading: $cmsContent['selected_events_heading'] ?? '',
            payWhatYouLikeMessage: $cmsContent['pay_what_you_like_message'] ?? '',
            clearButtonText: $cmsContent['clear_button_text'] ?? '',
            continueExploringText: $cmsContent['continue_exploring_text'] ?? '',
            paymentOverviewHeading: $cmsContent['payment_overview_heading'] ?? '',
            items: $itemViewModels,
            subtotal: self::formatPrice((float)$programData['subtotal']),
            taxLabel: $cmsContent['tax_label'] ?? '',
            taxAmount: self::formatPrice((float)$programData['taxAmount']),
            total: self::formatPrice((float)$programData['total']),
            checkoutButtonText: $cmsContent['checkout_button_text'] ?? '',
            canCheckout: $itemViewModels !== [],
            isLoggedIn: $isLoggedIn,
        );
    }

    /**
     * @param array{program: ?\App\Models\Program, items: array<int, array<string, mixed>>, subtotal: float, taxAmount: float, total: float} $programData
     * @param array<string, string> $cmsContent
     */
    public static function toCheckoutViewModel(array $programData, array $cmsContent, bool $isLoggedIn): CheckoutPageViewModel
    {
        $itemViewModels = array_map([self::class, 'toCheckoutItemViewModel'], $programData['items']);

        return new CheckoutPageViewModel(
            pageTitle: $cmsContent['page_title'] ?? '',
            backButtonText: $cmsContent['back_button_text'] ?? '',
            paymentOverviewHeading: $cmsContent['payment_overview_heading'] ?? '',
            personalInfoHeading: $cmsContent['personal_info_heading'] ?? '',
            personalInfoSubtext: $cmsContent['personal_info_subtext'] ?? '',
            firstNameLabel: $cmsContent['first_name_label'] ?? '',
            firstNamePlaceholder: $cmsContent['first_name_placeholder'] ?? '',
            lastNameLabel: $cmsContent['last_name_label'] ?? '',
            lastNamePlaceholder: $cmsContent['last_name_placeholder'] ?? '',
            emailLabel: $cmsContent['email_label'] ?? '',
            emailPlaceholder: $cmsContent['email_placeholder'] ?? '',
            paymentMethodsHeading: $cmsContent['payment_methods_heading'] ?? '',
            saveDetailsLabel: $cmsContent['save_details_label'] ?? '',
            saveDetailsSubtext: $cmsContent['save_details_subtext'] ?? '',
            payButtonText: $cmsContent['pay_button_text'] ?? '',
            items: $itemViewModels,
            subtotal: self::formatPrice((float)$programData['subtotal']),
            taxLabel: $cmsContent['tax_label'] ?? '',
            taxAmount: self::formatPrice((float)$programData['taxAmount']),
            total: self::formatPrice((float)$programData['total']),
            isLoggedIn: $isLoggedIn,
        );
    }

    public static function formatPrice(float $amount): string
    {
        return '€' . number_format($amount, 2, '.', '');
    }

    /**
     * @param array{subtotal: float, taxAmount: float, total: float} $programData
     * @return array{subtotal: string, taxAmount: string, total: string}
     */
    public static function formatTotals(array $programData): array
    {
        return [
            'subtotal' => self::formatPrice((float)$programData['subtotal']),
            'taxAmount' => self::formatPrice((float)$programData['taxAmount']),
            'total' => self::formatPrice((float)$programData['total']),
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
