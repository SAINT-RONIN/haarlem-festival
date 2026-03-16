<?php
/**
 * Jazz artist listen-now section.
 *
 * @var \App\ViewModels\Jazz\JazzArtistDetailPageViewModel $viewModel
 */
?>

<section class="w-full bg-royal-blue px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-10 sm:py-12 md:py-14">
    <div class="flex flex-col gap-6 sm:gap-7">
        <header class="max-w-4xl flex flex-col gap-3 sm:gap-4">
            <h2 class="text-white/80 text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold font-['Montserrat'] leading-tight">
                <?= htmlspecialchars($viewModel->listenHeading) ?>
            </h2>
            <h3 class="text-white text-2xl sm:text-3xl md:text-4xl font-normal font-['Montserrat']">
                <?= htmlspecialchars($viewModel->listenSubheading) ?>
            </h3>
            <p class="text-white text-base sm:text-lg md:text-xl font-normal font-['Montserrat'] leading-relaxed">
                <?= htmlspecialchars($viewModel->listenDescription) ?>
            </p>
        </header>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-5">
            <?php foreach ($viewModel->tracks as $track): ?>
                <article class="bg-white rounded-xl shadow-lg p-3 sm:p-4 flex flex-col sm:flex-row gap-4 sm:gap-5">
                    <img src="<?= htmlspecialchars($track->imageUrl) ?>"
                         alt="<?= htmlspecialchars($track->title) ?> track artwork"
                         class="w-full sm:w-28 md:w-36 lg:w-40 aspect-square rounded-lg object-cover">

                    <div class="flex-1 flex flex-col gap-2">
                        <h4 class="text-black text-xl sm:text-2xl font-normal font-['Montserrat'] leading-tight">
                            <?= htmlspecialchars($track->title) ?>
                        </h4>
                        <p class="text-black/70 text-base sm:text-lg font-normal font-['Montserrat']">
                            <?= htmlspecialchars($track->album) ?>
                        </p>
                        <p class="text-black text-base sm:text-lg font-normal font-['Montserrat'] leading-relaxed">
                            <?= htmlspecialchars($track->description) ?>
                        </p>

                        <div class="mt-1 p-3 sm:p-4 bg-gray-50 rounded-lg shadow-sm flex items-center gap-3">
                            <button type="button"
                                    class="w-10 h-10 rounded-full bg-royal-blue flex items-center justify-center shadow-md">
                                <svg class="w-4 h-4 text-white ml-0.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M8 5v14l11-7z"></path>
                                </svg>
                                <span class="sr-only"><?= htmlspecialchars($viewModel->listenPlayButtonLabel) ?></span>
                            </button>
                            <div class="flex-1 flex flex-col gap-2">
                                <span class="text-black/70 text-sm sm:text-base font-normal font-['Montserrat']">
                                    <?= htmlspecialchars($viewModel->listenPlayExcerptText) ?>
                                </span>
                                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-royal-blue <?= htmlspecialchars($track->progressClass) ?>"></div>
                                </div>
                            </div>
                            <span class="text-black text-sm font-normal font-['Inter']">
                                <?= htmlspecialchars($track->duration) ?>
                            </span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
