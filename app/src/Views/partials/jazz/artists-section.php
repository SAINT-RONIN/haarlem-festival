<?php
/**
 * Artists section partial for Jazz page.
 *
 * @var \App\ViewModels\ArtistsData $artistsData
 */

if (!isset($artistsData) && isset($viewModel) && property_exists($viewModel, 'artistsData')) {
    $artistsData = $viewModel->artistsData;
}

if (!isset($artistsData)) {
    return;
}
?>

<!-- Artists Section -->
<section id="artists" class="self-stretch px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-10 md:py-12 flex flex-col justify-start items-start gap-4 sm:gap-5">
    <!-- Heading -->
    <div class="self-stretch flex flex-col justify-start items-start gap-3 sm:gap-3.5">
        <h2 class="self-stretch text-royal-blue text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold font-['Montserrat']">
            <?= htmlspecialchars($artistsData->headingText) ?>
        </h2>

        <!-- Artist Cards Grid -->
        <div class="self-stretch flex flex-col lg:flex-row justify-center items-stretch gap-6 lg:gap-12">
            <?php foreach ($artistsData->artists as $artist): ?>
                <?php require __DIR__ . '/artist-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Pagination -->
    <div class="self-stretch p-2.5 bg-white rounded-md flex justify-center items-center gap-4 sm:gap-6">
        <!-- Left Arrow -->
        <button class="w-7 h-7 flex items-center justify-center rotate-180" aria-label="Previous page">
            <svg class="w-7 h-7" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.5 7L17.5 14L10.5 21" stroke="#1A2A40" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        <!-- Page Info -->
        <p class="text-center text-royal-blue text-lg sm:text-xl font-normal font-['Montserrat'] leading-7">
            Artist <?= $artistsData->currentPage ?>-<?= min($artistsData->currentPage * 3, $artistsData->totalArtists) ?> of <?= $artistsData->totalArtists ?>
        </p>

        <!-- Dots -->
        <div class="flex justify-start items-center gap-2.5">
            <?php for ($i = 1; $i <= $artistsData->totalPages; $i++): ?>
                <div class="w-7 h-7 rounded-full <?= $i === $artistsData->currentPage ? 'bg-royal-blue' : 'bg-zinc-300' ?>"></div>
            <?php endfor; ?>
        </div>

        <!-- Right Arrow -->
        <button class="w-7 h-7 flex items-center justify-center" aria-label="Next page">
            <svg class="w-7 h-7" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.5 7L17.5 14L10.5 21" stroke="#1A2A40" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
</section>
