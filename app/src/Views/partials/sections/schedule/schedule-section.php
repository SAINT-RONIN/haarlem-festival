<?php
/**
 * Reusable schedule section partial.
 * Used by Storytelling, Jazz, Dance, and History pages.
 *
 * @var \App\ViewModels\Schedule\ScheduleSectionViewModel $scheduleSection
 */

use App\Helpers\CmsOutputHelper;

if (!isset($scheduleSection) && isset($viewModel) && property_exists($viewModel, 'scheduleSection')) {
    $scheduleSection = $viewModel->scheduleSection;
}

if (!isset($scheduleSection) || $scheduleSection === null) {
    return;
}

$schedule = $scheduleSection;
$sectionId = $schedule->sectionId ?? 'schedule';
?>

<!-- Schedule Section -->
<section id="<?= htmlspecialchars($sectionId) ?>"
         class="w-full flex flex-col justify-start items-center pt-12"
         aria-labelledby="<?= htmlspecialchars($sectionId) ?>-heading">

    <!-- Calendar Header -->
    <div class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 pb-3.5 flex flex-col justify-start items-start gap-3.5 overflow-hidden">

        <!-- Title Row -->
        <header class="w-full flex flex-col justify-start items-start gap-3.5">
            <div class="w-full flex flex-col sm:flex-row justify-start items-start sm:items-baseline gap-2.5">
                <h2 id="<?= htmlspecialchars($sectionId) ?>-heading"
                    class="text-slate-800 text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold font-['Montserrat']">
                    <?= CmsOutputHelper::text($schedule->title) ?>
                </h2>
                <span class="text-slate-800 text-2xl sm:text-3xl md:text-4xl font-bold font-['Montserrat'] shrink-0">
                    <?= CmsOutputHelper::text($schedule->year) ?>
                </span>
            </div>
        </header>

        <!-- Filters Row -->
        <?php if ($schedule->showFilters): ?>
            <div class="w-full flex flex-col justify-start items-start gap-2.5">
                <div class="w-full flex justify-between items-end">
                    <div class="flex-1 flex justify-start items-center gap-2.5">
                        <button type="button"
                                class="px-4 sm:px-6 py-2 sm:py-3 bg-slate-800 rounded-2xl border border-slate-800 flex justify-center items-center gap-6 sm:gap-9 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-600 focus-visible:ring-offset-2">
                        <span class="flex justify-start items-center gap-2.5">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 aria-hidden="true" focusable="false">
                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                            </svg>
                            <span class="text-center text-white text-lg sm:text-xl font-medium font-['Montserrat'] leading-7">
                                <?= CmsOutputHelper::text($schedule->filtersButtonText) ?>
                            </span>
                        </span>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Additional Info Box -->
        <?php if ($schedule->showAdditionalInfo && !empty($schedule->additionalInfoBody)): ?>
            <aside class="w-stretch p-3.5 bg-blue-100 rounded-2xl border-[3px] border-[#1C398E] flex flex-col justify-center items-start gap-2.5">
                <div class="w-full flex justify-start items-end gap-1.5">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-blue-900 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         aria-hidden="true" focusable="false">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <strong class="flex-1 text-blue-900 text-lg sm:text-xl font-medium font-['Montserrat'] leading-7">
                        <?= CmsOutputHelper::text($schedule->additionalInfoTitle) ?>
                    </strong>
                </div>
                <div class="w-full text-blue-900 text-lg sm:text-xl font-medium font-['Montserrat'] leading-7">
                    <?= CmsOutputHelper::html($schedule->additionalInfoBody) ?>
                </div>
            </aside>
        <?php endif; ?>
    </div>

    <!-- Event Count -->
    <?php if ($schedule->showEventCount): ?>
        <div class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 flex justify-center items-center gap-2.5">
            <p class="flex-1 text-right text-slate-800 text-base sm:text-lg font-medium font-['Montserrat'] leading-7"
               aria-live="polite">
                <?= (int)$schedule->eventCount ?> <?= CmsOutputHelper::text($schedule->eventCountLabel) ?>
            </p>
        </div>
    <?php endif; ?>

    <!-- Schedule Days Grid -->
    <?php
    $dayCount = count($schedule->days);
// For 1-4 days: single row with equal columns
// For 5+ days: wrap into multiple rows with max 4 per row
$gridClasses = $dayCount <= 4
    ? 'lg:flex-row lg:flex-nowrap'
    : 'lg:flex-row lg:flex-wrap';
$itemClasses = $dayCount <= 4
    ? 'lg:flex-1'
    : 'lg:w-[calc(25%-1.5rem)] lg:min-w-[280px]';
?>
    <div class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 pt-2.5 pb-8 sm:pb-12">
        <ul class="w-full flex flex-col <?= $gridClasses ?> justify-center items-start gap-4 sm:gap-6 lg:gap-12"
            role="list" aria-label="Schedule days">
            <?php foreach ($schedule->days as $dayIndex => $day): ?>
                <?php $dayItemClasses = $itemClasses; ?>
                <?php require __DIR__ . '/schedule-day-column.php'; ?>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
