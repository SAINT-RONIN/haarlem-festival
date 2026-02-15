<?php
/**
 * History tour schedule section with filter button and day columns.
 *
 * Expects a \App\ViewModels\History\HistoryPageViewModel as $viewModel
 * and uses its scheduleData property.
 *
 * @var \App\ViewModels\History\HistoryPageViewModel $viewModel
 */

use App\ViewModels\History\ScheduleData;

/** @var ScheduleData $schedule */
$schedule = $viewModel->scheduleData;
?>
<section id="schedule" class="self-stretch px-6 lg:px-24 py-12 flex flex-col justify-start items-start gap-6 overflow-hidden">
    <div class="self-stretch text-slate-800 text-5xl font-bold font-['Montserrat'] leading-[62px]">
        <?= htmlspecialchars($schedule->headingText) ?>
    </div>
    <div class="inline-flex justify-start items-center gap-2.5">
        <button class="px-6 py-3 bg-slate-800 rounded-2xl outline outline-1 outline-offset-[-1px] outline-slate-800 flex justify-center items-center gap-9">
            <div class="flex justify-start items-center gap-2.5">
                <div class="w-6 h-6 relative overflow-hidden">
                    <div class="w-5 h-5 left-[2px] top-[3px] absolute outline outline-2 outline-offset-[-1px] outline-white"></div>
                </div>
                <span class="text-center text-white text-xl font-medium font-['Montserrat'] leading-7">
                    <?= htmlspecialchars($schedule->filterLabel) ?>
                </span>
            </div>
        </button>
    </div>
    <div class="self-stretch flex flex-col lg:flex-row justify-start items-start gap-6 lg:gap-8 xl:gap-10">
        <?php foreach ($schedule->days as $day): ?>
            <?php require __DIR__ . '/schedule-day.php'; ?>
        <?php endforeach; ?>
    </div>
</section>
