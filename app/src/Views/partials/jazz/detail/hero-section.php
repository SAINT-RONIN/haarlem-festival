<?php
/**
 * Jazz artist hero section.
 *
 * @var \App\ViewModels\Jazz\JazzArtistDetailPageViewModel $viewModel
 */
?>

<section class="self-stretch px-1 sm:px-2 pb-1 sm:pb-2 flex flex-col justify-center items-center gap-3 sm:gap-5"
         aria-labelledby="artist-detail-hero-heading">
    <div class="self-stretch min-h-[500px] h-[calc(100vh-0.5rem)] sm:h-[calc(100vh-1rem)] rounded-bl-[20px] rounded-br-[20px] sm:rounded-bl-[30px] sm:rounded-br-[30px] md:rounded-bl-[50px] md:rounded-br-[50px] flex flex-col justify-between items-end relative overflow-hidden hero-background-base"
         style="background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.65)), url('<?= htmlspecialchars($viewModel->heroBackgroundImageUrl) ?>');">
        <?php
        $isSticky = false;
        require __DIR__ . '/../../navbar.php';
        ?>

        <div class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 pt-2 sm:pt-4 pb-6 sm:pb-8 md:pb-10 flex flex-col gap-6 sm:gap-8">
            <div class="flex flex-col gap-2 sm:gap-3">
                <h1 id="artist-detail-hero-heading"
                    class="text-white text-4xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl font-normal font-['Montserrat'] leading-tight">
                    <?= htmlspecialchars($viewModel->heroTitle) ?>
                </h1>
                <p class="text-white text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-light font-['Montserrat'] leading-tight">
                    <?= htmlspecialchars($viewModel->heroSubtitle) ?>
                </p>
            </div>

            <div class="flex flex-col lg:flex-row lg:items-center gap-2.5 sm:gap-4 text-white text-base sm:text-lg md:text-xl font-normal font-['Montserrat']">
                <span><?= htmlspecialchars($viewModel->originText) ?></span>
                <span class="hidden lg:inline text-white/90">•</span>
                <span><?= htmlspecialchars($viewModel->formedText) ?></span>
                <span class="hidden lg:inline text-white/90">•</span>
                <span><?= htmlspecialchars($viewModel->performancesText) ?></span>
            </div>
        </div>

        <div class="self-stretch flex flex-col justify-start items-start">
            <div class="pl-2 sm:pl-3 md:pl-4 lg:pl-16 xl:pl-24 pr-2 sm:pr-3 md:pr-4 py-2 sm:py-3 md:py-4 bg-sand rounded-tr-[12px] sm:rounded-tr-[15px] md:rounded-tr-[25px] lg:rounded-tr-[35px] flex flex-wrap justify-start items-center gap-2.5 sm:gap-4">
                <a href="/jazz"
                   class="px-4 sm:px-5 py-2.5 sm:py-3 bg-royal-blue hover:bg-red rounded-xl sm:rounded-2xl text-sand text-base sm:text-lg font-normal font-['Montserrat'] transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                    <?= htmlspecialchars($viewModel->heroBackButtonText) ?>
                </a>
                <a href="#<?= htmlspecialchars($viewModel->performancesSectionId) ?>"
                   class="px-4 sm:px-6 py-2.5 sm:py-3 bg-red hover:bg-royal-blue rounded-xl sm:rounded-2xl text-white text-base sm:text-lg font-normal font-['Montserrat'] transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                    <?= htmlspecialchars($viewModel->heroReserveButtonText) ?>
                </a>
            </div>
        </div>
    </div>
</section>
