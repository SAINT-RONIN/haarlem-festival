<?php
/**
 * Global Intro Split section partial.
 * Text on one side, image on the other.
 *
 * Required variable:
 * @var \App\ViewModels\IntroSplitSectionData $introSplitSection
 */
?>

<section id="intro" class="self-stretch px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-12 md:py-16 lg:py-20 xl:py-12 flex flex-col lg:flex-row justify-center items-stretch gap-6 sm:gap-8 md:gap-10 lg:gap-12">
    <div class="flex-1 flex flex-col justify-center items-start gap-4 sm:gap-5 md:gap-6">
        <h2 class="text-gray-900 text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold leading-tight">
            <?= htmlspecialchars($introSplitSection->headingText) ?>
        </h2>
        <p class="text-gray-700 text-base sm:text-lg md:text-xl leading-relaxed">
            <?= htmlspecialchars($introSplitSection->bodyText) ?>
        </p>
    </div>

    <div class="flex-1 flex justify-center items-center">
        <img src="<?= htmlspecialchars($introSplitSection->imageUrl) ?>"
             alt="<?= htmlspecialchars($introSplitSection->imageAltText) ?>"
             class="w-full h-auto rounded-2xl object-cover">
    </div>
</section>

