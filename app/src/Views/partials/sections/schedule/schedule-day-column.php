<?php
/**
 * Reusable schedule day column partial.
 *
 * @var \App\ViewModels\Schedule\ScheduleDayViewModel $day
 * @var int $dayIndex
 * @var string $dayItemClasses Dynamic classes for responsive layout
 * @var \App\ViewModels\Schedule\ $schedule
 */

use App\Helpers\CmsOutputHelper;

$dayId = 'schedule-day-' . $dayIndex;
$itemClasses = $dayItemClasses ?? 'lg:flex-1';
?>

<li class="w-full <?= $itemClasses ?>"
    data-filter-day="<?= htmlspecialchars(strtolower($day->dayName)) ?>">
    <article
            class="w-full px-4 sm:px-5 py-3.5 bg-[#ECE6DD] rounded-2xl sm:rounded-3xl flex flex-col justify-start items-center gap-3.5"
            aria-labelledby="<?= $dayId ?>-heading">

        <!-- Day Header -->
        <header class="w-full py-1.5 border-b border-slate-800 flex justify-center items-center gap-2.5">
            <h3 id="<?= $dayId ?>-heading"
                class="flex-1 text-center text-slate-800 text-xl sm:text-2xl font-bold font-['Montserrat'] leading-6">
                <?= htmlspecialchars($day->dayName) ?>
            </h3>
        </header>

        <!-- Event Cards -->
        <ul class="w-full flex flex-col justify-start items-start gap-3.5 overflow-hidden" role="list"
            aria-label="<?= htmlspecialchars($day->dayName) ?> events">
            <?php if (empty($day->events)): ?>
                <li class="w-full p-4 sm:p-5 bg-white rounded-2xl sm:rounded-3xl flex justify-center items-center">
                    <p class="text-slate-500 text-sm sm:text-base"><?= CmsOutputHelper::text($schedule->noEventsText) ?></p>
                </li>
            <?php else: ?>
                <?php foreach ($day->events as $eventIndex => $event): ?>
                    <?php require __DIR__ . '/schedule-event-card.php'; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </article>
</li>
