<?php
/**
 * Schedule day column partial - Single day column with date header and event cards.
 *
 * @var array $day Day data with keys: dayName, dayNumber, monthShort, eventCount, sessions
 * @var array $cms CMS content including global_ui section
 */

$dayName = htmlspecialchars($day['dayName']);
$dayNumber = htmlspecialchars((string)$day['dayNumber']);
$monthShort = htmlspecialchars($day['monthShort']);
$eventCount = (int)$day['eventCount'];
$sessions = $day['sessions'];
$global = $cms['global_ui'];
?>

<div class="w-full lg:flex-1 bg-slate-800 rounded-[12px] sm:rounded-[16px] md:rounded-[20px] inline-flex flex-col justify-start items-start overflow-hidden">
    <!-- Day Header -->
    <div class="self-stretch p-3 sm:p-4 md:p-5 bg-slate-800 inline-flex justify-start items-start gap-2 sm:gap-2.5 overflow-hidden">
        <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 bg-stone-100 rounded-md sm:rounded-lg inline-flex flex-col justify-center items-center">
            <div class="text-slate-800 text-[10px] sm:text-xs font-semibold uppercase leading-tight tracking-wide"><?php echo $monthShort; ?></div>
            <div class="text-slate-800 text-lg sm:text-xl md:text-2xl font-bold leading-tight"><?php echo $dayNumber; ?></div>
        </div>
        <div class="flex-1 inline-flex flex-col justify-start items-start">
            <div class="self-stretch justify-start text-stone-100 text-base sm:text-lg md:text-xl font-semibold leading-snug"><?php echo $dayName; ?></div>
            <div class="self-stretch justify-start text-stone-100 text-xs sm:text-sm font-normal leading-tight"><?php echo $eventCount; ?> <?= htmlspecialchars($global['label_events_count']) ?></div>
        </div>
    </div>

    <!-- Events List -->
    <div class="self-stretch p-3 sm:p-4 md:p-5 bg-slate-800 flex flex-col justify-start items-start gap-1.5 sm:gap-2 md:gap-2.5 overflow-hidden">
        <?php if (empty($sessions)): ?>
            <div class="self-stretch p-3 sm:p-4 md:p-5 bg-white rounded-[12px] sm:rounded-[16px] md:rounded-[20px] inline-flex justify-center items-center">
                <span class="text-slate-500 text-xs sm:text-sm"><?= htmlspecialchars($global['label_no_events']) ?></span>
            </div>
        <?php else: ?>
            <?php foreach ($sessions as $session): ?>
                <?php require __DIR__ . '/schedule-event-card.php'; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

