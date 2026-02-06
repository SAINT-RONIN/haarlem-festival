<?php
/**
 * Schedule day column partial - Single day column with date header and event cards.
 *
 * @var array $day Day data with keys: dayName, dayNumber, monthShort, eventCount, sessions
 */

$dayName = htmlspecialchars($day['dayName']);
$dayNumber = htmlspecialchars((string)$day['dayNumber']);
$monthShort = htmlspecialchars($day['monthShort']);
$eventCount = (int)$day['eventCount'];
$sessions = $day['sessions'];
?>

<div class="w-full lg:flex-1 bg-slate-800 rounded-[20px] inline-flex flex-col justify-start items-start overflow-hidden">
    <!-- Day Header -->
    <div class="self-stretch p-5 bg-slate-800 inline-flex justify-start items-start gap-2.5 overflow-hidden">
        <div class="w-14 h-14 bg-stone-100 rounded-lg inline-flex flex-col justify-center items-center">
            <div class="relative">
                <div class="justify-start text-slate-800 text-xs font-semibold uppercase leading-tight tracking-wide"><?php echo $monthShort; ?></div>
            </div>
            <div class="relative">
                <div class="justify-start text-slate-800 text-2xl font-bold leading-none"><?php echo $dayNumber; ?></div>
            </div>
        </div>
        <div class="flex-1 inline-flex flex-col justify-start items-start">
            <div class="self-stretch justify-start text-stone-100 text-lg md:text-xl font-semibold leading-normal"><?php echo $dayName; ?></div>
            <div class="self-stretch justify-start text-stone-100 text-sm font-normal leading-normal"><?php echo $eventCount; ?> events</div>
        </div>
    </div>

    <!-- Events List -->
    <div class="self-stretch p-5 bg-slate-800 flex flex-col justify-start items-start gap-2.5 overflow-hidden">
        <?php if (empty($sessions)): ?>
            <div class="self-stretch p-5 bg-white rounded-[20px] inline-flex justify-center items-center">
                <span class="text-slate-500 text-sm">No events scheduled</span>
            </div>
        <?php else: ?>
            <?php foreach ($sessions as $session): ?>
                <?php require __DIR__ . '/schedule-event-card.php'; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

