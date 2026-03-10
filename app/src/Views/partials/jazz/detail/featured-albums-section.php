<?php
/**
 * Jazz artist featured albums section.
 *
 * @var \App\ViewModels\Jazz\JazzArtistDetailPageViewModel $viewModel
 */
?>

<section class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-10 md:py-12 flex flex-col gap-6">
    <header class="max-w-4xl flex flex-col gap-3 sm:gap-4">
        <h2 class="text-royal-blue text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold font-['Montserrat'] leading-tight">
            <?= htmlspecialchars($viewModel->albumsHeading) ?>
        </h2>
        <p class="text-royal-blue/80 text-base sm:text-lg md:text-xl font-normal font-['Montserrat'] leading-relaxed">
            <?= htmlspecialchars($viewModel->albumsDescription) ?>
        </p>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 lg:gap-8">
        <?php foreach ($viewModel->albums as $album): ?>
            <article class="flex flex-col gap-4">
                <div class="rounded-2xl overflow-hidden shadow-xl">
                    <img src="<?= htmlspecialchars($album->imageUrl) ?>"
                         alt="<?= htmlspecialchars($album->title) ?> album cover"
                         class="w-full aspect-square object-cover">
                </div>
                <h3 class="text-royal-blue text-2xl sm:text-3xl font-bold font-['Montserrat']">
                    <?= htmlspecialchars($album->title) ?>
                </h3>
                <p class="text-royal-blue/80 text-base sm:text-lg font-normal font-['Montserrat'] leading-relaxed">
                    <?= htmlspecialchars($album->description) ?>
                </p>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="px-4 py-2 rounded-full bg-royal-blue text-sand text-sm font-normal font-['Montserrat']">
                        <?= htmlspecialchars($album->year) ?>
                    </span>
                    <span class="px-4 py-2 rounded-full bg-royal-blue text-sand text-sm font-normal font-['Montserrat']">
                        <?= htmlspecialchars($album->tag) ?>
                    </span>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
