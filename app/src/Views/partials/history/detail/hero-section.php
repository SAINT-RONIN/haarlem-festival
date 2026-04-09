<?php
/**
 * Renders the hero section for a single historical location page.
 *
 * @var \App\ViewModels\History\HistoricalLocationViewModel $viewModel
 */
use App\View\ViewRenderer;

$hero       = $viewModel->locationHero;
$isLoggedIn = $viewModel->globalUi->isLoggedIn;
?>

<section class="self-stretch px-1 sm:px-2 pb-1 sm:pb-2 flex flex-col justify-center items-center gap-3 sm:gap-5"
         aria-labelledby="history-location-heading">
    <div class="self-stretch min-h-[500px] h-[calc(100vh-0.5rem)] sm:h-[calc(100vh-1rem)]
                rounded-bl-[20px] rounded-br-[20px] sm:rounded-bl-[30px] sm:rounded-br-[30px] md:rounded-bl-[50px] md:rounded-br-[50px]
                flex flex-col justify-between items-end relative bg-dynamic-fade"
         style="--bg-url: url('<?= htmlspecialchars($hero->backgroundImageUrl) ?>')"
         role="img" aria-label="<?= htmlspecialchars($hero->mainTitle) ?> hero background">

        <?php ViewRenderer::render(__DIR__ . '/../../navbar.php', [
            'isSticky'    => false,
            'currentPage' => 'history',
            'isLoggedIn'  => $isLoggedIn,
        ]); ?>

        <!-- Hero Content - Title, Subtitle, and Map image -->
        <div class="self-stretch px-3 sm:px-4 md:px-8 lg:px-16 xl:px-24 flex flex-col justify-center items-start">
            <div class="w-full flex flex-col lg:flex-row justify-center items-center lg:items-center gap-4 md:gap-6 lg:gap-10">
                <div class="flex-1 flex flex-col justify-center items-center gap-2 sm:gap-3">
                    <h1 id="history-location-heading"
                        class="self-stretch text-white text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl 2xl:text-7xl font-normal leading-tight">
                        <?= htmlspecialchars($hero->mainTitle) ?>
                    </h1>
                    <p class="self-stretch text-white text-sm sm:text-base md:text-xl lg:text-2xl xl:text-3xl 2xl:text-4xl font-light leading-snug">
                        <?= htmlspecialchars($hero->subtitle) ?>
                    </p>
                </div>

                <?php if ($hero->mapImageUrl !== ''): ?>
                    <div class="w-3/4 max-w-md md:max-w-lg lg:max-w-xl flex justify-center items-center">
                        <img src="<?= htmlspecialchars($hero->mapImageUrl) ?>"
                             alt="Map showing the location of <?= htmlspecialchars($hero->mainTitle) ?>"
                             class="w-3/4 h-auto  shadow-lg object-cover">
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Back to history CTA -->
        <div class="self-stretch flex flex-col justify-start items-start">
            <div class="self-stretch h-4 sm:h-6 md:h-10 lg:h-16 xl:h-20" aria-hidden="true"></div>
            <div class="pr-2 sm:pr-3 md:pr-4 lg:pr-12 xl:pr-24 pl-2 sm:pl-3 md:pl-4 py-2 sm:py-3 md:py-4 lg:py-5 bg-sand rounded-tr-[12px] sm:rounded-tr-[15px] md:rounded-tr-[25px] lg:rounded-tr-[35px] flex justify-end items-end">
                <a href="<?= htmlspecialchars($hero->buttonLink) ?>"
                   class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 md:px-5 py-2 sm:py-2.5 md:py-3.5
                          bg-royal-blue hover:bg-red rounded-md sm:rounded-lg md:rounded-xl lg:rounded-2xl
                          text-sand text-xs sm:text-sm md:text-base lg:text-lg font-normal whitespace-nowrap
                          transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 md:w-5 md:h-5 flex-shrink-0" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                    <?= htmlspecialchars($hero->buttonText) ?>
                </a>
            </div>
        </div>
    </div>
</section>

<script src="/assets/js/menu-toggle.js"></script>
