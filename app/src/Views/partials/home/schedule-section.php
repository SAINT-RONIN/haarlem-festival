<?php
/**
 * Schedule section partial - Container for schedule day columns.
 *
 * @var \App\ViewModels\HomeScheduleDayViewModel[] $scheduleDays
 * @var \App\ViewModels\HomeScheduleSectionViewModel $schedulePreviewSection
 * @var \App\ViewModels\GlobalUiData $globalUi
 */

use App\Helpers\CmsOutputHelper;
use App\View\ViewRenderer;

?>

<!-- Schedule Section -->
<section id="schedule" class="w-full px-2 sm:px-4 md:px-8 lg:px-16 xl:px-24 py-4 sm:py-6 md:py-10 lg:py-12 flex flex-col justify-center items-center gap-3 sm:gap-4 md:gap-5" aria-labelledby="schedule-heading">
    <header class="self-stretch flex flex-col justify-start items-start">
        <h2 id="schedule-heading" class="self-stretch justify-start text-royal-blue text-xl sm:text-2xl md:text-4xl lg:text-5xl xl:text-6xl 2xl:text-7xl font-bold leading-tight"><?= CmsOutputHelper::text($schedulePreviewSection->title) ?></h2>
        <p class="self-stretch justify-center text-royal-blue text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl font-normal"><?= CmsOutputHelper::text($schedulePreviewSection->subtitlePrimary) ?></p>
        <p class="self-stretch justify-center text-royal-blue text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl font-normal"><?= CmsOutputHelper::text($schedulePreviewSection->subtitleSecondary) ?></p>
    </header>

    <!-- Day Columns - flex-1 for each column to distribute evenly, items-start so they size to content -->
    <div class="self-stretch py-1 sm:py-2.5 flex flex-col lg:flex-row justify-center items-start gap-3 sm:gap-4 md:gap-6 lg:gap-8 xl:gap-12" role="list" aria-label="Festival schedule by day">
        <?php foreach ($scheduleDays as $day): ?>
            <?php ViewRenderer::render(__DIR__ . '/schedule-day-column.php', [
                'day' => $day,
                'globalUi' => $globalUi,
            ]); ?>
        <?php endforeach; ?>
    </div>
</section>
