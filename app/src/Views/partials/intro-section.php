<?php
/**
 * Intro section partial - "What is Haarlem Festival?" section.
 * @var \App\ViewModels\HomeIntroSectionViewModel $introSection
 */

use App\Helpers\CmsOutputHelper;

?>

<!-- Intro Section -->
<section id="intro" class="w-full px-2 sm:px-4 md:px-8 lg:px-16 xl:px-24 py-4 sm:py-6 md:py-10 lg:py-12 flex flex-col lg:flex-row justify-center items-stretch gap-4 sm:gap-6 md:gap-8 lg:gap-12" aria-labelledby="intro-heading">
    <div class="flex-1 flex flex-col justify-start items-start gap-2 sm:gap-3 md:gap-4 lg:gap-5">
        <h2 id="intro-heading" class="self-stretch text-royal-blue text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl 2xl:text-7xl font-bold leading-tight"><?= CmsOutputHelper::text($introSection->title) ?></h2>
        <p class="self-stretch text-royal-blue text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl font-semibold leading-snug"><?= CmsOutputHelper::text($introSection->tagline) ?></p>
        <div class="self-stretch text-royal-blue text-xs sm:text-sm md:text-base lg:text-lg xl:text-xl font-normal leading-relaxed space-y-2 sm:space-y-3 md:space-y-4">
            <?= CmsOutputHelper::html($introSection->descriptionHtml) ?>
        </div>
        <a href="<?= htmlspecialchars($introSection->buttonUrl) ?>" class="w-full sm:w-auto px-4 sm:px-6 md:px-10 lg:px-12 py-2 sm:py-2.5 md:py-3 lg:py-3.5 bg-red hover:bg-royal-blue rounded-lg sm:rounded-xl md:rounded-2xl inline-flex justify-center items-center gap-2.5 transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
            <span class="text-white text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl font-normal"><?= CmsOutputHelper::text($introSection->buttonLabel) ?></span>
        </a>
    </div>
    <figure class="w-full lg:w-auto lg:flex-1">
        <img class="w-full h-[200px] sm:h-[250px] md:h-[350px] lg:h-full lg:min-h-[300px] rounded-xl sm:rounded-2xl md:rounded-3xl object-cover" src="<?= htmlspecialchars($introSection->imageUrl) ?>" alt="<?= htmlspecialchars($introSection->imageAlt) ?>">
    </figure>
</section>
