<?php
/**
 * History tour card partial.
 *
 * Displays a history walking tour event card with:
 * - Location description (instead of venue name)
 * - Date
 * - Ticket type label (e.g., "Group ticket - best value for 4 people")
 * - Time with clock icon (larger, prominent)
 * - Language labels
 * - Price with "from" prefix and CTA
 *
 * @var object|array $event Event data with properties:
 *   - eventSessionId
 *   - title (location description for history)
 *   - dateDisplay
 *   - isoDate
 *   - timeDisplay (just start time for history)
 *   - startTimeIso
 *   - ticketLabel (e.g., "Group ticket - best value for 4 people")
 *   - priceDisplay
 *   - ctaLabel
 *   - ctaUrl
 *   - labels (array of strings)
 * @var int $eventIndex
 * @var int $dayIndex
 */

$eventId = 'history-event-' . $dayIndex . '-' . $eventIndex;

// $event is always a ScheduleEventCardViewModel — normalized before reaching the view
?>

<li class="w-full">
    <article
            class="w-full max-w-xs p-5 bg-white rounded-3xl inline-flex flex-col justify-start items-start overflow-hidden gap-3"
            aria-labelledby="<?= $eventId ?>-title">

        <!-- Information Section (Location, Date, Ticket Type) -->
        <dl class="w-full inline-flex justify-start items-center gap-2.5">
            <div class="flex-1 inline-flex flex-col justify-center items-start gap-3.5">

                <!-- Location/Description (acts as title for history tours) -->
                <div class="w-full inline-flex justify-start items-start gap-[5px]">
                    <dt class="sr-only">Location</dt>
                    <dd class="inline-flex items-start gap-[5px]">
                        <svg class="w-4 h-4 text-slate-800 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                             aria-hidden="true" focusable="false">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <span id="<?= $eventId ?>-title"
                              class="flex-1 text-slate-800 text-lg font-light font-['Montserrat'] leading-5">
                            <?= htmlspecialchars($event->title ?? '') ?>
                        </span>
                    </dd>
                </div>

                <!-- Date -->
                <div class="w-full inline-flex justify-start items-center gap-[5px]">
                    <dt class="sr-only">Date</dt>
                    <dd class="inline-flex items-center gap-[5px]">
                        <svg class="w-4 h-4 text-slate-800 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                             aria-hidden="true" focusable="false">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <time datetime="<?= htmlspecialchars($event->isoDate ?? '') ?>"
                              class="flex-1 text-slate-800 text-lg font-light font-['Montserrat'] leading-4">
                            <?= htmlspecialchars($event->dateDisplay ?? '') ?>
                        </time>
                    </dd>
                </div>

                <!-- Ticket Type Label (history-specific) -->
                <?php if (!empty($event->ticketLabel)): ?>
                    <div class="w-full inline-flex justify-start items-center gap-[5px]">
                        <dt class="sr-only">Ticket type</dt>
                        <dd class="inline-flex items-center gap-[5px]">
                            <!-- Price tag icon -->
                            <svg class="w-4 h-4 text-slate-800 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                 aria-hidden="true" focusable="false">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                <line x1="7" y1="7" x2="7.01" y2="7"></line>
                            </svg>
                            <span class="flex-1 text-slate-800 text-lg font-light font-['Montserrat'] leading-4">
                                <?= htmlspecialchars($event->ticketLabel) ?>
                            </span>
                        </dd>
                    </div>
                <?php endif; ?>
            </div>
        </dl>

        <!-- Time and Labels Row -->
        <div class="w-full inline-flex flex-col justify-start items-start gap-2">
            <!-- Prominent Time Display -->
            <div class="w-full inline-flex justify-start items-start gap-1">
                <svg class="w-6 h-6 text-slate-800 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                     aria-hidden="true" focusable="false">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <time datetime="<?= htmlspecialchars($event->startTimeIso ?? '') ?>"
                      class="flex-1 text-slate-800 text-2xl font-semibold font-['Montserrat'] leading-6">
                    <?= htmlspecialchars($event->startTimeDisplay ?? '') ?>
                </time>
            </div>

            <!-- Language/Labels -->
            <?php if (!empty($event->labels)): ?>
                <ul class="w-48 inline-flex justify-end items-center gap-1 flex-wrap" aria-label="Tour options">
                    <?php foreach ($event->labels as $label): ?>
                        <li class="flex justify-start items-start gap-2.5">
                            <span class="px-2.5 py-[5px] bg-pink-700 rounded-[5px] flex justify-start items-center gap-2">
                                <span class="text-white text-base font-normal font-['Montserrat'] leading-6">
                                    <?= htmlspecialchars($label) ?>
                                </span>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- Price & CTA Row -->
        <div class="w-full inline-flex flex-col justify-start items-start gap-2.5">
            <div class="w-full h-px bg-gray-200" aria-hidden="true"></div>
            <div class="w-full inline-flex justify-between items-center">
                <span class="text-center text-slate-800 text-lg font-normal font-['Montserrat']">
                    <?= htmlspecialchars($event->priceDisplay ?? '') ?>
                </span>
                <a href="<?= htmlspecialchars($event->ctaUrl ?? '#') ?>"
                   class="px-2.5 py-[5px] bg-slate-800 hover:bg-red rounded-[10px] border border-slate-800 hover:border-red flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-600 focus-visible:ring-offset-2">
                    <span class="text-center text-stone-100 text-xl font-normal font-['Montserrat']">
                        <?= htmlspecialchars($event->ctaLabel ?? 'Add to program') ?>
                    </span>
                </a>
            </div>
        </div>
    </article>
</li>

