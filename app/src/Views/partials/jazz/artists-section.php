<?php
/**
 * Artists section partial for Jazz page.
 *
 * @var \App\ViewModels\ArtistsData $artistsData
 */

use App\View\ViewRenderer;

if (!isset($artistsData) && isset($viewModel) && property_exists($viewModel, 'artistsData')) {
    $artistsData = $viewModel->artistsData;
}

if (!isset($artistsData)) {
    return;
}

$artistPages = array_chunk($artistsData->artists, 3);
$artistPages = $artistPages !== [] ? $artistPages : [[]];
$initialEndArtist = $artistsData->totalArtists > 0 ? min(3, $artistsData->totalArtists) : 0;
?>

<!-- Artists Section -->
<section id="artists" class="self-stretch px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-10 md:py-12 flex flex-col justify-start items-start gap-4 sm:gap-5">
    <!-- Heading -->
    <div class="self-stretch flex flex-col justify-start items-start gap-3 sm:gap-3.5">
        <h2 class="self-stretch text-royal-blue text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold font-['Montserrat']">
            <?= htmlspecialchars($artistsData->headingText) ?>
        </h2>

        <!-- Artist Cards Grid -->
        <div class="self-stretch" data-jazz-artists-carousel data-total-pages="<?= count($artistPages) ?>" data-total-artists="<?= $artistsData->totalArtists ?>">
            <?php foreach ($artistPages as $pageIndex => $artistsPage): ?>
                <?php
                $isActivePage = $pageIndex === 0;
                $startArtist = ($pageIndex * 3) + 1;
                $endArtist = min($startArtist + count($artistsPage) - 1, $artistsData->totalArtists);
                ?>
                <div class="<?= $isActivePage ? '' : 'hidden' ?>"
                     data-jazz-artists-page
                     data-page-index="<?= $pageIndex ?>"
                     data-start-artist="<?= $artistsData->totalArtists > 0 ? $startArtist : 0 ?>"
                     data-end-artist="<?= $artistsData->totalArtists > 0 ? $endArtist : 0 ?>">
                    <div class="grid grid-cols-1 items-stretch gap-6 lg:grid-cols-3 lg:gap-12">
                        <?php if ($artistsPage !== []): ?>
                            <?php foreach ($artistsPage as $artist): ?>
                                <?php ViewRenderer::render(__DIR__ . '/artist-card.php', ['artist' => $artist]); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-span-full rounded-2xl border border-dashed border-zinc-300 bg-white/70 px-6 py-12 text-center">
                                <p class="text-lg font-medium text-royal-blue">Lineup cards will appear here once artists are added from CMS.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Pagination -->
    <div class="self-stretch p-2.5 bg-white rounded-md flex flex-wrap justify-center items-center gap-4 sm:gap-6" data-jazz-artists-controls>
        <!-- Left Arrow -->
        <button type="button"
                class="w-7 h-7 flex items-center justify-center rotate-180 disabled:opacity-40 disabled:cursor-not-allowed"
                aria-label="Previous page"
                data-jazz-artists-prev
                <?= count($artistPages) === 1 ? 'disabled' : '' ?>>
            <svg class="w-7 h-7" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.5 7L17.5 14L10.5 21" stroke="#1A2A40" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        <!-- Page Info -->
        <p class="text-center text-royal-blue text-lg sm:text-xl font-normal font-['Montserrat'] leading-7" data-jazz-artists-status>
            Artist <?= $artistsData->totalArtists > 0 ? '1' : '0' ?>-<?= $initialEndArtist ?> of <?= $artistsData->totalArtists ?>
        </p>

        <!-- Dots -->
        <div class="flex justify-start items-center gap-2.5">
            <?php for ($i = 1; $i <= $artistsData->totalPages; $i++): ?>
                <button type="button"
                        class="h-7 w-7 rounded-full transition-colors <?= $i === $artistsData->currentPage ? 'bg-royal-blue' : 'bg-zinc-300 hover:bg-zinc-400' ?>"
                        aria-label="Go to artist page <?= $i ?>"
                        data-jazz-artists-dot
                        data-page-index="<?= $i - 1 ?>"></button>
            <?php endfor; ?>
        </div>

        <!-- Right Arrow -->
        <button type="button"
                class="w-7 h-7 flex items-center justify-center disabled:opacity-40 disabled:cursor-not-allowed"
                aria-label="Next page"
                data-jazz-artists-next
                <?= count($artistPages) === 1 ? 'disabled' : '' ?>>
            <svg class="w-7 h-7" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.5 7L17.5 14L10.5 21" stroke="#1A2A40" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
</section>
<script src="/assets/js/jazz-artists-carousel.js"></script>
