<?php
/**
 * Venues section partial for Jazz page.
 *
 * @var \App\ViewModels\VenuesData $venuesData
 */
?>

<!-- Venues Section -->
<section class="self-stretch px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-10 md:py-12 bg-sand flex flex-col justify-center items-center gap-4 sm:gap-5 md:gap-6">
    <!-- Header -->
    <div class="self-stretch flex flex-col lg:flex-row justify-start items-start gap-6 lg:gap-12">
        <div class="flex-1 flex flex-col justify-start items-start gap-4 sm:gap-5 md:gap-6">
            <div class="self-stretch flex flex-col justify-start items-start gap-2 sm:gap-2.5">
                <h2 class="self-stretch text-royal-blue text-3xl sm:text-4xl md:text-5xl font-bold font-['Montserrat'] leading-tight md:leading-[62px]">
                    <?= htmlspecialchars($venuesData->headingText) ?>
                </h2>
                <p class="self-stretch text-royal-blue text-lg sm:text-xl font-normal font-['Montserrat']">
                    <?= htmlspecialchars($venuesData->subheadingText) ?>
                </p>
            </div>
            <p class="self-stretch text-royal-blue text-base sm:text-lg font-normal font-['Montserrat'] leading-relaxed md:leading-8">
                <?= htmlspecialchars($venuesData->descriptionText) ?>
            </p>
        </div>
        <div class="flex-1 h-48"></div>
    </div>

    <!-- Venue Cards -->
    <div class="self-stretch flex flex-col lg:flex-row justify-start items-stretch gap-6 lg:gap-12">
        <?php foreach ($venuesData->venues as $venue): ?>
            <?php require __DIR__ . '/venue-card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>

