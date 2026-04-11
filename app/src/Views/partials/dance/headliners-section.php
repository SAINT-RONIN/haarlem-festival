<?php
/**
 * Dance Headliners section — two featured artists displayed prominently.
 *
 * @var \App\ViewModels\Dance\HeadlinersData $headlinersData
 */

if (!isset($headlinersData)) {
    return;
}

if ($headlinersData->headliners === []) {
    return;
}
?>

<section id="dance-headliners" class="self-stretch px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-10 md:py-12 flex flex-col justify-start items-start gap-6 sm:gap-8">

    <h2 class="self-stretch text-slate-800 text-5xl sm:text-6xl md:text-7xl xl:text-8xl font-bold font-['Montserrat'] [text-shadow:_0px_4px_4px_rgb(0_0_0_/_0.25)]">
        <?= htmlspecialchars($headlinersData->headingText) ?>
    </h2>

    <div class="self-stretch grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
        <?php foreach ($headlinersData->headliners as $headliner): ?>
            <div class="flex flex-col justify-start items-start gap-4">

                <!-- Artist image -->
                <div class="self-stretch aspect-[4/3] rounded-[10px] overflow-hidden shadow-[0px_4px_4px_0px_rgba(0,0,0,0.25)]">
                    <?php if ($headliner->imageUrl !== ''): ?>
                        <img class="w-full h-full object-cover object-top"
                             src="<?= htmlspecialchars($headliner->imageUrl) ?>"
                             alt="<?= htmlspecialchars($headliner->name) ?>">
                    <?php else: ?>
                        <div class="w-full h-full bg-slate-200 flex items-center justify-center">
                            <span class="text-slate-400 text-xl font-['Montserrat']">Photo coming soon</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Name + genre -->
                <div class="self-stretch flex flex-col justify-start items-start gap-1">
                    <h3 class="self-stretch text-gray-800 text-4xl sm:text-5xl xl:text-6xl font-bold font-['Roboto'] leading-tight">
                        <?= htmlspecialchars($headliner->name) ?>
                    </h3>
                    <p class="self-stretch text-black text-2xl sm:text-3xl xl:text-4xl font-normal font-['Roboto']">
                        <?= htmlspecialchars($headliner->genre) ?>
                    </p>
                </div>

                <!-- View Artist button -->
                <?php if ($headliner->profileUrl !== '' && $headliner->profileUrl !== '/dance/'): ?>
                    <a href="<?= htmlspecialchars($headliner->profileUrl) ?>"
                       class="px-5 py-3.5 bg-pink-700 rounded-2xl outline outline-1 outline-offset-[-1px] outline-pink-700 inline-flex justify-center items-center gap-2 hover:bg-pink-800 transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2">
                        <span class="text-center text-stone-100 text-xl sm:text-2xl xl:text-3xl font-bold font-['Montserrat']">View Artist</span>
                        <svg class="w-2 h-3 flex-shrink-0" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.5 1L6.5 6L1.5 11" stroke="#F5F5F4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                <?php else: ?>
                    <div class="px-5 py-3.5 bg-slate-400 rounded-2xl inline-flex justify-center items-center gap-2 cursor-not-allowed">
                        <span class="text-center text-stone-100 text-xl sm:text-2xl xl:text-3xl font-bold font-['Montserrat']">Coming Soon</span>
                    </div>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    </div>

</section>
