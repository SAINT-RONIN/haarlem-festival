<?php
/**
 * Partial for rendering a single event card in the History tour schedule.
 *
 * Expects a \App\ViewModels\History\ScheduleCard instance as $event.
 *
 * @var \App\ViewModels\History\ScheduleCard $event
 */

use App\ViewModels\History\ScheduleCard;

/** @var ScheduleCard $event */
?>
<div class="self-stretch p-4 sm:p-5 bg-white rounded-3xl flex flex-col justify-start items-start gap-2.5">
    <!-- Time as title with icon -->
    <div class="self-stretch flex justify-start items-center gap-2">
        <img
            src="/assets/Icons/History/time-icon.svg"
            alt="Time icon"
            class="w-5 h-5 flex-shrink-0" />
        <h4 class="flex-1 text-slate-800 text-xl sm:text-2xl font-bold font-['Montserrat'] leading-6">
            <?= htmlspecialchars($event->time) ?>
        </h4>
    </div>

    <!-- Languages labels -->
    <?php if (!empty($event->languages)): ?>
        <div class="self-stretch flex flex-wrap justify-start sm:justify-start items-start gap-1">
            <?php foreach ($event->languages as $language): ?>
                <span class="px-2.5 py-1 bg-pink-700 rounded-md text-white text-xs sm:text-sm font-light font-['Montserrat']">
                    <?= htmlspecialchars($language) ?>
                </span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Venue with icon -->
    <div class="self-stretch flex items-start gap-2">
        <img
            src="/assets/Icons/History/location-icon.svg"
            alt="Location icon"
            class="w-5 h-5 flex-shrink-0 mt-0.5" />
        <p class="flex-1 text-slate-800 text-base sm:text-lg font-light font-['Montserrat']">
            <?= htmlspecialchars($event->venue) ?>
        </p>
    </div>

    <!-- Date with icon -->
    <div class="self-stretch flex items-start gap-2">
        <img
            src="/assets/Icons/History/date-icon.svg"
            alt="Date icon"
            class="w-5 h-5 flex-shrink-0 mt-0.5" />
        <p class="flex-1 text-slate-800 text-base sm:text-lg font-light font-['Montserrat']">
            <?= htmlspecialchars($event->date) ?>
        </p>
    </div>

    <!-- Group ticket info with price icon -->
    <div class="self-stretch flex items-start gap-2">
        <img
            src="/assets/Icons/History/price-icon.svg"
            alt="Ticket info icon"
            class="w-5 h-5 flex-shrink-0 mt-0.5" />
        <p class="flex-1 text-slate-800 text-base sm:text-lg font-light font-['Montserrat']">
            <?= htmlspecialchars($event->groupTicketInfo) ?>
        </p>
    </div>

    <!-- Price and CTA row -->
    <div class="self-stretch flex flex-col justify-start items-start gap-2.5">
        <div class="self-stretch border-b border-gray-200"></div>
        <div class="self-stretch flex justify-between items-center">
            <p class="text-slate-800 text-base sm:text-lg font-normal font-['Montserrat']">
                <?= htmlspecialchars($event->fromPrice) ?>
            </p>
            <button class="px-2.5 py-1.5 bg-slate-800 rounded-lg border border-slate-800 flex justify-center items-center">
                <span class="text-sand text-base sm:text-xl font-normal font-['Montserrat']">Add to program</span>
            </button>
        </div>
    </div>
</div>
