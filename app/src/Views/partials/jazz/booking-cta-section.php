<?php
/**
 * Booking call-to-action section partial for Jazz page.
 *
 * @var \App\ViewModels\BookingCallToActionData $bookingCtaData
 */
?>

<!-- Booking CTA Section -->
<section class="self-stretch relative bg-royal-blue shadow-md">
    <div class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-10 md:py-12 lg:py-16">
        <div class="flex flex-col justify-start items-start gap-6 sm:gap-8 max-w-4xl">
            <h2 class="text-white text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold font-['Montserrat'] leading-tight">
                <?= htmlspecialchars($bookingCtaData->headingText) ?>
            </h2>
            <p class="text-white text-base sm:text-lg md:text-xl font-normal font-['Montserrat'] leading-relaxed max-w-2xl">
                <?= htmlspecialchars($bookingCtaData->descriptionText) ?>
            </p>
        </div>
    </div>
</section>

