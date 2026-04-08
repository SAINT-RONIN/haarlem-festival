<?php
/**
 * Renders the schedule filter toggle button, reset button, and collapsible filter panel.
 * Shared by all event types that support schedule filtering.
 *
 * @var \App\ViewModels\Schedule\ScheduleSectionViewModel $schedule
 */

if (!$schedule->showFilters || empty($schedule->filterGroups)) {
    return;
}

$sectionId = $schedule->sectionId;
$filterPanelId = $sectionId . '-filters';
?>

<!-- Filters Row -->
<div class="w-full flex flex-col justify-start items-start gap-2.5"
     data-schedule-filters="<?= htmlspecialchars($sectionId) ?>">

    <!-- Filter & Reset Buttons -->
    <div class="w-full flex flex-wrap justify-between items-end gap-2.5">
        <div class="flex flex-wrap justify-start items-center gap-2.5">
            <!-- Toggle Filters Button -->
            <button type="button"
                    data-filter-toggle="<?= htmlspecialchars($filterPanelId) ?>"
                    aria-expanded="<?= $schedule->hasActiveFilters ? 'true' : 'false' ?>"
                    aria-controls="<?= htmlspecialchars($filterPanelId) ?>"
                    class="px-4 sm:px-6 py-2 sm:py-3 bg-slate-800 rounded-2xl border border-slate-800 flex justify-center items-center gap-6 sm:gap-9 transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-600 focus-visible:ring-offset-2">
                <span class="flex justify-start items-center gap-2.5">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         aria-hidden="true" focusable="false">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    <span class="text-center text-white text-lg sm:text-xl font-medium leading-7">
                        <?= htmlspecialchars($schedule->filtersButtonText) ?>
                    </span>
                </span>
                <!-- Close X icon (visible when open) -->
                <span class="filter-close-icon <?= $schedule->hasActiveFilters ? '' : 'hidden' ?>" aria-hidden="true">
                    <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </span>
            </button>

            <!-- Reset Button (hidden by default, shown when filters are open) -->
            <button type="button"
                    data-filter-reset="<?= htmlspecialchars($sectionId) ?>"
                    class="<?= $schedule->hasActiveFilters ? '' : 'hidden' ?> px-4 sm:px-6 py-2 sm:py-3 bg-red-500 hover:bg-red-600 rounded-2xl flex justify-center items-center gap-2.5 transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400 focus-visible:ring-offset-2">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     aria-hidden="true" focusable="false">
                    <polyline points="1 4 1 10 7 10"></polyline>
                    <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                </svg>
                <span class="text-center text-white text-lg sm:text-xl font-medium leading-7">
                    <?= htmlspecialchars($schedule->resetButtonText) ?>
                </span>
            </button>
        </div>

        <!-- Event Count (if shown) -->
        <?php if ($schedule->showEventCount): ?>
            <div class="flex justify-center items-center gap-2.5">
                <span class="text-right text-slate-800 text-lg sm:text-xl font-medium leading-7"
                      data-filter-event-count="<?= htmlspecialchars($sectionId) ?>"
                      aria-live="polite">
                    <?= (int) $schedule->eventCount ?> <?= htmlspecialchars($schedule->eventCountLabel ?? '') ?>
                </span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Filter Panel (hidden by default, open when filters are active) -->
    <div id="<?= htmlspecialchars($filterPanelId) ?>"
         class="<?= $schedule->hasActiveFilters ? '' : 'hidden' ?> w-full p-4 sm:p-6 bg-slate-800 rounded-3xl border border-slate-800 flex flex-col justify-start items-start gap-5"
         role="region"
         aria-label="Schedule filters">

        <?php foreach ($schedule->filterGroups as $group): ?>
            <div class="flex flex-col justify-start items-start gap-2">
                <!-- Group Label -->
                <span class="text-gray-200 text-lg sm:text-xl font-medium leading-5">
                    <?= htmlspecialchars($group->label) ?>
                </span>
                <!-- Filter Options -->
                <div class="flex flex-wrap justify-start items-center gap-2.5"
                     data-filter-group="<?= htmlspecialchars($group->key) ?>"
                     role="radiogroup"
                     aria-label="<?= htmlspecialchars($group->label) ?>">
                    <?php foreach ($group->options as $option): ?>
                        <button type="button"
                                data-filter-value="<?= htmlspecialchars($option->value) ?>"
                                role="radio"
                                aria-checked="<?= $option->isActive ? 'true' : 'false' ?>"
                                class="px-3.5 py-2.5 rounded-[10px] flex justify-center items-center gap-2.5 transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-400 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-800 <?= $option->isActive ? 'bg-pink-700 text-stone-100' : 'bg-stone-100 text-slate-800 hover:bg-stone-200' ?>">
                            <span class="text-center text-lg font-normal leading-7">
                                <?= htmlspecialchars($option->label) ?>
                            </span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
