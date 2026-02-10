<?php
/** @var \App\ViewModels\ScheduleEventData $event */
?>
<div class="self-stretch p-4 sm:p-5 bg-white rounded-3xl flex flex-col justify-start items-start gap-2.5">
    <div class="self-stretch flex flex-col justify-start items-start gap-2.5">
        <div class="self-stretch border-b border-gray-200"></div>
        <div class="self-stretch flex justify-between items-center">
            <p class="text-center text-royal-blue text-base sm:text-lg font-normal font-['Montserrat']"><?= htmlspecialchars($event->price) ?></p>
            <button class="px-2.5 py-1.5 bg-royal-blue rounded-lg border border-royal-blue flex justify-center items-center">
                <span class="text-center text-sand text-base sm:text-xl font-normal font-['Montserrat']">Add to program</span>
            </button>
        </div>
    </div>
    <div class="self-stretch flex justify-start items-start gap-1">
        <h4 class="flex-1 text-royal-blue text-xl sm:text-2xl font-bold font-['Montserrat'] leading-6"><?= htmlspecialchars($event->artistName) ?></h4>
        <div class="px-2.5 py-1.5 bg-red rounded-md"><span class="text-white text-xs sm:text-sm font-light font-['Montserrat']"><?= htmlspecialchars($event->genre) ?></span></div>
    </div>
    <div class="self-stretch flex flex-col gap-2.5">
        <p class="text-royal-blue text-base sm:text-lg font-light font-['Montserrat']"><?= htmlspecialchars($event->venue) ?></p>
        <p class="text-royal-blue text-base sm:text-lg font-light font-['Montserrat']"><?= htmlspecialchars($event->date) ?></p>
        <p class="text-royal-blue text-base sm:text-lg font-light font-['Montserrat']"><?= htmlspecialchars($event->time) ?></p>
    </div>
</div>

