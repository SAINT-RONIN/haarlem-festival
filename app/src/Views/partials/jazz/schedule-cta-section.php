<?php
/**
 * Schedule call-to-action section partial for Jazz page.
 *
 * @var \App\ViewModels\ScheduleCallToActionData $scheduleCtaData
 */
?>

<!-- Schedule CTA Section -->
<section class="self-stretch px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-10 md:py-12 rounded-lg flex flex-col justify-center items-start gap-4">
    <div class="self-stretch p-6 sm:p-8 md:p-10 lg:p-12 bg-royal-blue rounded-2xl flex flex-col justify-start items-start gap-6 sm:gap-7">
        <div class="self-stretch flex flex-col justify-start items-start gap-4 sm:gap-5">
            <h2 class="self-stretch text-white text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold font-['Montserrat'] leading-tight">
                <?= htmlspecialchars($scheduleCtaData->headingText) ?>
            </h2>
            <div class="self-stretch flex flex-col lg:flex-row justify-start items-start gap-4">
                <p class="flex-1 text-white text-lg sm:text-xl md:text-2xl font-normal font-['Montserrat']">
                    <?= htmlspecialchars($scheduleCtaData->descriptionText) ?>
                </p>
                <div class="flex-1 h-7"></div>
            </div>
        </div>
        <a href="<?= htmlspecialchars($scheduleCtaData->buttonLink) ?>"
           class="p-3 sm:p-3.5 bg-sand hover:bg-red rounded-2xl border-2 border-royal-blue hover:border-red inline-flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
            <span class="text-center text-royal-blue hover:text-white text-lg sm:text-xl font-normal font-['Montserrat'] transition-colors duration-200">
                <?= htmlspecialchars($scheduleCtaData->buttonText) ?>
            </span>
        </a>
    </div>
</section>

