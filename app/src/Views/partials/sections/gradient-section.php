<?php
/**
 * Global Gradient section partial.
 * Background image with gradient overlay.
 *
 * Required variable:
 * @var \App\ViewModels\GradientSectionData $gradientSection
 */

if (!isset($gradientSection)) {
    return;
}
?>

<section class="self-stretch px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-12 md:py-16 lg:py-20 xl:py-12 inline-flex flex-col justify-center items-center gap-2.5 overflow-hidden">
    <div class="self-stretch px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-12 sm:py-16 md:py-20 lg:py-24 xl:py-32 rounded-[20px] sm:rounded-[30px] md:rounded-[40px] flex flex-col justify-center items-start gap-8 sm:gap-10 md:gap-12 lg:gap-14 overflow-hidden bg-cover bg-center"
         style="background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.7)), url('<?= htmlspecialchars($gradientSection->backgroundImageUrl) ?>');">
        <h2 class="w-full max-w-5xl justify-start text-white text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl 2xl:text-7xl font-bold font-['Montserrat'] leading-tight md:leading-[63px]">
            <?= htmlspecialchars($gradientSection->headingText) ?>
        </h2>
        <p class="w-full max-w-5xl justify-start text-white text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl font-normal font-['Montserrat']">
            <?= htmlspecialchars($gradientSection->subheadingText) ?>
        </p>
    </div>
</section>
