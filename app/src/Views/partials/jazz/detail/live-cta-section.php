<?php
/**
 * Jazz artist live call-to-action section.
 *
 * @var \App\ViewModels\Jazz\JazzArtistDetailPageViewModel $viewModel
 */
?>

<section class="w-full bg-royal-blue border-t border-white/10 px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-10 sm:py-12 md:py-14">
    <div class="w-full max-w-6xl mx-auto flex flex-col items-center text-center gap-6">
        <h2 class="text-white text-3xl sm:text-4xl md:text-5xl font-semibold font-['Montserrat'] leading-tight">
            <?= htmlspecialchars($viewModel->cta->liveCtaHeading) ?>
        </h2>
        <p class="max-w-4xl text-white text-base sm:text-lg md:text-xl font-normal font-['Montserrat'] leading-relaxed">
            <?= htmlspecialchars($viewModel->cta->liveCtaDescription) ?>
        </p>
        <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-4">
            <a href="#<?= htmlspecialchars($viewModel->cta->performancesSectionId) ?>"
               class="px-6 sm:px-8 py-3 sm:py-4 bg-white hover:bg-red rounded-[10px] shadow-xl text-black hover:text-white text-lg sm:text-xl font-normal font-['Montserrat'] transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                <?= htmlspecialchars($viewModel->cta->liveCtaBookButtonText) ?>
            </a>
            <a href="<?= htmlspecialchars($viewModel->cta->liveCtaScheduleButtonUrl) ?>"
               class="px-6 sm:px-8 py-3 sm:py-4 rounded-[10px] border-2 border-white text-white hover:bg-white hover:text-royal-blue text-lg sm:text-xl font-normal font-['Montserrat'] transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2">
                <?= htmlspecialchars($viewModel->cta->liveCtaScheduleButtonText) ?>
            </a>
        </div>
    </div>
</section>
