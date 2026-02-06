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
            <h2 class="self-stretch justify-start text-slate-800 text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold leading-tight lg:leading-[70px]"><?= htmlspecialchars($schedule['schedule_main_title'] ?? 'Events schedule') ?></h2>
            <p class="self-stretch justify-center text-slate-800 text-xl md:text-2xl lg:text-3xl font-normal"><?= htmlspecialchars($schedule['schedule_subtitle_1'] ?? 'Discover what\'s happening each day') ?></p>
            <p class="self-stretch justify-center text-slate-800 text-xl md:text-2xl lg:text-3xl font-normal"><?= htmlspecialchars($schedule['schedule_subtitle_2'] ?? 'We invite you to join us for music, stories, tour, and great food.') ?></p>
        </div>

        <div class="py-2.5 flex flex-col lg:flex-row justify-center items-start gap-6 lg:gap-12 overflow-hidden">
            <?php foreach ($scheduleDays as $day): ?>
                <?php require __DIR__ . '/schedule-day-column.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
