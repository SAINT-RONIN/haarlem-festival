<?php
/**
 * Single artist card partial.
 *
 * @var \App\ViewModels\ArtistCardData $artist
 */
?>

<div class="flex-1 bg-white rounded-2xl shadow-lg flex flex-col justify-start items-start overflow-hidden">
    <img class="self-stretch h-64 sm:h-72 md:h-80 lg:h-96 object-cover" src="<?= htmlspecialchars($artist->imageUrl) ?>" alt="<?= htmlspecialchars($artist->name) ?>">
    <div class="self-stretch p-4 sm:p-5 md:p-6 flex flex-col justify-start items-start gap-4 sm:gap-5">
        <div class="self-stretch flex flex-col justify-start items-start">
            <h3 class="self-stretch text-royal-blue text-xl sm:text-2xl font-semibold font-['Montserrat']"><?= htmlspecialchars($artist->name) ?></h3>
            <p class="self-stretch text-royal-blue text-lg sm:text-xl font-light font-['Montserrat']"><?= htmlspecialchars($artist->genre) ?></p>
        </div>
        <p class="self-stretch text-royal-blue text-base sm:text-lg font-normal font-['Montserrat']"><?= htmlspecialchars($artist->description) ?></p>
        <div class="self-stretch flex flex-col justify-start items-start">
            <p class="self-stretch text-royal-blue text-lg sm:text-xl font-medium font-['Montserrat']"><?= $artist->performanceCount ?> PERFORMANCE<?= $artist->performanceCount > 1 ? 'S' : '' ?></p>
            <div class="self-stretch flex justify-start items-start gap-5">
                <p class="flex-1 text-royal-blue text-base sm:text-lg font-normal font-['Montserrat']"><?= htmlspecialchars($artist->firstPerformance) ?></p>
                <?php if ($artist->morePerformancesText): ?>
                    <p class="text-royal-blue text-lg sm:text-xl font-normal font-['Montserrat']"><?= htmlspecialchars($artist->morePerformancesText) ?></p>
                <?php endif; ?>
            </div>
        </div>
        <button class="self-stretch h-11 px-4 bg-royal-blue hover:bg-royal-blue-dark rounded-md flex justify-between items-center transition-colors duration-200">
            <span class="flex-1 text-left text-white text-lg sm:text-xl font-normal font-['Montserrat'] leading-5">View full profile</span>
            <span class="text-center text-white text-xl sm:text-2xl font-normal font-['Montserrat'] leading-7">+</span>
        </button>
    </div>
</div>
