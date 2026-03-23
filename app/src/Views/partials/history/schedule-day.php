<?php
/**
 * Column partial for a single day in the History tour schedule.
 *
 * Expects a \App\ViewModels\History\ScheduleDayData instance as $day.
 *
 * @var \App\ViewModels\History\ScheduleDayData $day
 */

use App\ViewModels\History\ScheduleDayData;

/** @var ScheduleDayData $day */
?>
<div class="flex-1 px-2.5 py-3.5 bg-stone-200 rounded-3xl flex flex-col justify-start items-center gap-3.5 overflow-hidden">
    <div class="self-stretch py-[5px] border-b border-slate-800 inline-flex justify-center items-center gap-2.5">
        <h3 class="flex-1 text-center text-slate-800 text-2xl font-bold font-['Montserrat'] leading-6">
            <?= htmlspecialchars($day->dayName) ?>
        </h3>
    </div>
    <div class="self-stretch flex flex-col justify-start items-start gap-3">
        <?php foreach ($day->events as $event): ?>
            <?php require __DIR__ . '/schedule-card.php'; ?>
        <?php endforeach; ?>
    </div>
</div>
