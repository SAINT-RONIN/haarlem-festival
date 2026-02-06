<?php
/**
 * Schedule section partial - Container for schedule day columns.
 *
 * @var array $scheduleDays Array of day data
 */
?>

<!-- Schedule Section -->
<div id="schedule" class="w-full px-4 md:px-12 lg:px-24 py-8 md:py-12 inline-flex flex-col justify-center items-center gap-5 overflow-hidden">
    <div class="self-stretch flex flex-col justify-start items-start">
        <h2 class="self-stretch justify-start text-slate-800 text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold leading-tight lg:leading-[70px]">Events schedule</h2>
        <p class="self-stretch justify-center text-slate-800 text-xl md:text-2xl lg:text-3xl font-normal">Discover what's happening each day</p>
        <p class="self-stretch justify-center text-slate-800 text-xl md:text-2xl lg:text-3xl font-normal">We invite you to join us for music, stories, tour, and great food.</p>
    </div>

    <!-- Day Columns - flex-1 for each column to distribute evenly, items-start so they size to content -->
    <div class="self-stretch py-2.5 inline-flex flex-col lg:flex-row justify-center items-start gap-6 lg:gap-12 overflow-hidden">
        <?php foreach ($scheduleDays as $day): ?>
            <?php require __DIR__ . '/schedule-day-column.php'; ?>
        <?php endforeach; ?>
    </div>
</div>

