<?php
/**
 * @var array $scheduleDays
 * @var array $cms
 */
$schedule = $cms['schedule_section'] ?? [];
?>

<div id="schedule" class="w-full py-8 md:py-12 inline-flex flex-col justify-center items-center gap-5 overflow-hidden">
    <div class="hf-container w-full">
        <div class="flex flex-col justify-start items-start">
            <h2 class="self-stretch justify-start text-slate-800 text-4xl sm:text-5xl md:text-6xl font-bold leading-tight"><?= htmlspecialchars($schedule['schedule_main_title']) ?></h2>
            <p class="self-stretch justify-center text-slate-800 text-xl md:text-2xl font-normal leading-snug"><?= htmlspecialchars($schedule['schedule_subtitle_1']) ?></p>
            <p class="self-stretch justify-center text-slate-800 text-xl md:text-2xl font-normal leading-snug"><?= htmlspecialchars($schedule['schedule_subtitle_2']) ?></p>
        </div>

        <div class="py-2.5 flex flex-col lg:flex-row justify-center items-start gap-6 lg:gap-12 overflow-hidden">
            <?php foreach ($scheduleDays as $day): ?>
                <?php require __DIR__ . '/schedule-day-column.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
