<?php
/**
 * Jazz artist performances section.
 *
 * @var \App\ViewModels\Jazz\JazzArtistDetailPageViewModel $viewModel
 */
?>

<section id="<?= htmlspecialchars($viewModel->cta->performancesSectionId) ?>" class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-10 sm:py-12 md:py-14">
    <div class="flex flex-col gap-6 sm:gap-7">
        <header class="max-w-4xl flex flex-col gap-3 sm:gap-4">
            <h2 class="text-royal-blue text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold font-['Montserrat'] leading-tight">
                <?= htmlspecialchars($viewModel->cta->performancesHeading) ?>
            </h2>
            <p class="text-royal-blue/80 text-base sm:text-lg md:text-xl font-normal font-['Montserrat'] leading-relaxed">
                <?= htmlspecialchars($viewModel->cta->performancesDescription) ?>
            </p>
        </header>

        <ul class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-6" role="list" aria-label="<?= htmlspecialchars($viewModel->hero->heroTitle) ?> performances">
            <?php foreach ($viewModel->performances as $eventIndex => $event): ?>
                <?php $dayIndex = 0; ?>
                <?php require __DIR__ . '/../../sections/schedule/schedule-event-card.php'; ?>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
