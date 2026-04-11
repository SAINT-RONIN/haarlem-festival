<?php
/**
 * Dance Supporting Artists section — grid of supporting artists.
 *
 * @var \App\ViewModels\Dance\SupportingArtistsData $supportingArtistsData
 */

if (!isset($supportingArtistsData)) {
    return;
}

if ($supportingArtistsData->artists === []) {
    return;
}
?>

<section id="dance-supporting-artists" class="self-stretch px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-10 md:py-12 flex flex-col justify-start items-start gap-6 sm:gap-8">

    <h2 class="self-stretch text-black text-4xl sm:text-5xl md:text-6xl xl:text-7xl font-bold font-['Montserrat']">
        <?= htmlspecialchars($supportingArtistsData->headingText) ?>
    </h2>

    <div class="self-stretch grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
        <?php foreach ($supportingArtistsData->artists as $artist): ?>
            <div class="flex flex-col justify-start items-start gap-3">

                <!-- Artist image -->
                <div class="self-stretch aspect-square rounded-lg overflow-hidden shadow-sm">
                    <?php if ($artist->imageUrl !== ''): ?>
                        <img class="w-full h-full object-cover object-top"
                             src="<?= htmlspecialchars($artist->imageUrl) ?>"
                             alt="<?= htmlspecialchars($artist->name) ?>">
                    <?php else: ?>
                        <div class="w-full h-full bg-slate-200 flex items-center justify-center">
                            <span class="text-slate-400 text-sm font-['Montserrat'] text-center px-2">Photo coming soon</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Name + genre -->
                <div class="self-stretch flex flex-col justify-start items-start gap-0.5">
                    <h3 class="self-stretch text-gray-800 text-2xl sm:text-3xl xl:text-4xl font-bold font-['Roboto'] leading-tight">
                        <?= htmlspecialchars($artist->name) ?>
                    </h3>
                    <p class="self-stretch text-black text-xl sm:text-2xl xl:text-4xl font-normal font-['Roboto']">
                        <?= htmlspecialchars($artist->genre) ?>
                    </p>
                </div>

                <!-- View Artist button -->
                <?php if ($artist->profileUrl !== '' && $artist->profileUrl !== '/dance/'): ?>
                    <a href="<?= htmlspecialchars($artist->profileUrl) ?>"
                       class="px-3.5 py-2.5 bg-pink-700 rounded-2xl outline outline-1 outline-offset-[-1px] outline-pink-700 inline-flex justify-center items-center gap-2 hover:bg-pink-800 transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2">
                        <span class="text-center text-stone-100 text-lg sm:text-xl font-bold font-['Montserrat']">View Artist</span>
                        <svg class="w-1.5 h-3 flex-shrink-0" viewBox="0 0 6 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L5 6L1 11" stroke="#F5F5F4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                <?php else: ?>
                    <div class="px-3.5 py-2.5 bg-slate-400 rounded-2xl inline-flex justify-center items-center cursor-not-allowed">
                        <span class="text-center text-stone-100 text-lg sm:text-xl font-bold font-['Montserrat']">Coming Soon</span>
                    </div>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    </div>

</section>
