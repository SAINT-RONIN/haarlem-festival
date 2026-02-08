<?php
/**
 * Intro split section partial for storytelling page.
 * Text on left, image on right (desktop). Stacked on mobile.
 *
 * Required variable (set before including this partial):
 * @var \App\ViewModels\IntroSplitSectionData $introSplitSection
 */
?>

<!-- Intro Split Section (Text + Image) -->
<section
        class="self-stretch px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-10 md:py-12 inline-flex flex-col md:flex-row justify-center items-center gap-6 sm:gap-8 md:gap-10 lg:gap-12 overflow-hidden">
    <!-- Text Content -->
    <div class="flex-1 inline-flex flex-col justify-start items-start gap-4 sm:gap-5">
        <h2 class="self-stretch justify-center text-slate-800 text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl 2xl:text-7xl font-bold font-['Montserrat'] leading-tight lg:leading-[70px]">
            <?= htmlspecialchars($introSplitSection->headingText) ?>
        </h2>
        <div class="self-stretch justify-center text-slate-800 text-base sm:text-lg md:text-xl font-normal font-['Montserrat'] leading-relaxed md:leading-7">
            <?= nl2br(htmlspecialchars($introSplitSection->bodyText)) ?>
        </div>
    </div>
    <!-- Image -->
    <div class="flex-1 self-stretch flex justify-center items-center">
        <img
                class="w-full max-w-lg md:max-w-none h-full rounded-[20px] sm:rounded-[30px] md:rounded-[40px] object-cover"
                src="<?= htmlspecialchars($introSplitSection->imageUrl) ?>"
                alt="Stories in Haarlem">
    </div>
</section>