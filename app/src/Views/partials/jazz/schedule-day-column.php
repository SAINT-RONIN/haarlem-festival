<?php
/**
 * Schedule day column partial.
 *
 * @var \App\ViewModels\ScheduleDayData $day
 */
?>

<div class="flex-1 px-3 sm:px-4 md:px-5 py-3 sm:py-3.5 bg-stone-200 rounded-3xl flex flex-col justify-start items-center gap-3.5">
    <div class="self-stretch py-1 sm:py-1.5 border-b border-royal-blue flex justify-center items-center gap-2.5">
        <h3 class="flex-1 text-center text-royal-blue text-xl sm:text-2xl font-bold font-['Montserrat'] leading-6"><?= htmlspecialchars($day->dayName) ?></h3>
    </div>
    <div class="self-stretch flex flex-col justify-start items-start gap-3">
        <?php foreach ($day->events as $event): ?>
            <?php require __DIR__ . '/schedule-event-card.php'; ?>
        <?php endforeach; ?>
    </div>
</div>

