<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Models\CheckoutMainContent;
use App\Models\ProgramMainContent;

/**
 * Maps raw CMS arrays into Checkout and Program content models.
 */
final class CheckoutContentMapper
{
    /** Maps raw CMS data to a CheckoutMainContent model. */
    public static function mapCheckout(array $raw): CheckoutMainContent
    {
        return new CheckoutMainContent(
            pageTitle: $raw['page_title'] ?? null,
            backButtonText: $raw['back_button_text'] ?? null,
            paymentOverviewHeading: $raw['payment_overview_heading'] ?? null,
            personalInfoHeading: $raw['personal_info_heading'] ?? null,
            personalInfoSubtext: $raw['personal_info_subtext'] ?? null,
            firstNameLabel: $raw['first_name_label'] ?? null,
            firstNamePlaceholder: $raw['first_name_placeholder'] ?? null,
            lastNameLabel: $raw['last_name_label'] ?? null,
            lastNamePlaceholder: $raw['last_name_placeholder'] ?? null,
            emailLabel: $raw['email_label'] ?? null,
            emailPlaceholder: $raw['email_placeholder'] ?? null,
            paymentMethodsHeading: $raw['payment_methods_heading'] ?? null,
            saveDetailsLabel: $raw['save_details_label'] ?? null,
            saveDetailsSubtext: $raw['save_details_subtext'] ?? null,
            payButtonText: $raw['pay_button_text'] ?? null,
            taxLabel: $raw['tax_label'] ?? null,
        );
    }

    /** Maps raw CMS data to a ProgramMainContent model. */
    public static function mapProgram(array $raw): ProgramMainContent
    {
        return new ProgramMainContent(
            pageTitle: $raw['page_title'] ?? null,
            selectedEventsHeading: $raw['selected_events_heading'] ?? null,
            payWhatYouLikeMessage: $raw['pay_what_you_like_message'] ?? null,
            clearButtonText: $raw['clear_button_text'] ?? null,
            continueExploringText: $raw['continue_exploring_text'] ?? null,
            paymentOverviewHeading: $raw['payment_overview_heading'] ?? null,
            taxLabel: $raw['tax_label'] ?? null,
            checkoutButtonText: $raw['checkout_button_text'] ?? null,
        );
    }
}
