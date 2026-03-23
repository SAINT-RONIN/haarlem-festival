<?php
/**
 * Schedule section partial for Jazz page.
 *
 * @var \App\ViewModels\ScheduleData $scheduleData
 */
?>

<!-- Schedule Section -->
<section id="schedule" class="py-8 sm:py-10 md:py-12 bg-sand flex flex-col justify-start items-start">
    <div class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 pb-3.5 flex flex-col justify-start items-start gap-3.5">
        <div class="self-stretch flex flex-col justify-start items-start gap-2.5">
            <div class="self-stretch flex justify-start items-start gap-2.5">
                <h2 class="flex-1 text-royal-blue text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold font-['Montserrat']"><?= htmlspecialchars($scheduleData->headingText) ?></h2>
                <span class="text-royal-blue text-2xl sm:text-3xl md:text-4xl font-bold font-['Montserrat']"><?= htmlspecialchars($scheduleData->year) ?></span>
            </div>
        </div>
        <div class="self-stretch flex flex-col justify-start items-start gap-2.5">
            <div class="self-stretch flex justify-between items-end">
                <div class="flex-1 flex justify-start items-center gap-2.5">
                    <button class="px-4 sm:px-6 py-2 sm:py-3 bg-royal-blue rounded-2xl border border-royal-blue flex justify-center items-center gap-6 sm:gap-9">
                        <div class="flex justify-start items-center gap-2.5">
                            <span class="text-center text-white text-lg sm:text-xl font-medium font-['Montserrat'] leading-7"><?= htmlspecialchars($scheduleData->filterLabel) ?></span>
                        </div>
                    </button>
                </div>
                <p class="text-right text-royal-blue text-lg sm:text-xl font-medium font-['Montserrat'] leading-7"><?= htmlspecialchars($scheduleData->totalEventsText) ?></p>
            </div>
        </div>
    </div>
    
    <div class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 pt-2.5 flex flex-col lg:flex-row justify-center items-start gap-6 lg:gap-12">
        <?php foreach ($scheduleData->days as $day): ?>
            <?php require __DIR__ . '/schedule-day-column.php'; ?>
        <?php endforeach; ?>
    </div>
</section>

