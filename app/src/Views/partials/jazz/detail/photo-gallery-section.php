<?php
/**
 * Gumbo Kings photo gallery section.
 *
 * @var \App\ViewModels\Jazz\GumboKingsDetailPageViewModel $viewModel
 */
?>

<section class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-10 md:py-12 flex flex-col gap-6">
    <header class="max-w-4xl flex flex-col gap-3 sm:gap-4">
        <h2 class="text-royal-blue text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold font-['Montserrat'] leading-tight">
            Photo Gallery
        </h2>
        <p class="text-royal-blue/80 text-base sm:text-lg md:text-xl font-normal font-['Montserrat'] leading-relaxed">
            Experience the energy and passion of Gumbo Kings through these performance and portrait photographs.
        </p>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 sm:gap-6">
        <?php foreach ($viewModel->galleryImages as $index => $galleryImage): ?>
            <figure class="rounded-2xl overflow-hidden shadow-xl">
                <img src="<?= htmlspecialchars($galleryImage) ?>"
                     alt="Gumbo Kings gallery image <?= (int)$index + 1 ?>"
                     class="w-full aspect-[552/450] object-cover">
            </figure>
        <?php endforeach; ?>
    </div>
</section>
