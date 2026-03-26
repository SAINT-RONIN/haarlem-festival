<?php
/**
 * Reusable schedule event card partial.
 *
 * @var \App\ViewModels\Schedule\ScheduleEventCardViewModel $event
 * @var int $eventIndex
 * @var int $dayIndex
 */

$eventId = 'event-' . $dayIndex . '-' . $eventIndex;
$isHistoryEvent = $event->eventTypeSlug === 'history';
$isJazzEvent = $event->eventTypeSlug === 'jazz';
?>

<li class="w-full">
    <article
        class="w-full p-4 sm:p-5 bg-white rounded-2xl sm:rounded-3xl inline-flex flex-col justify-start items-start overflow-hidden gap-2"
        aria-labelledby="<?= $eventId ?>-title">

        <!-- Title & Labels Row -->
        <div class="w-full inline-flex justify-start items-start gap-[5px]">
            <div class="flex-1 flex flex-col justify-start items-start gap-1.5">
                <?php if ($isHistoryEvent): ?>
                    <!-- History: time icon + time-based title -->
                    <h4 id="<?= $eventId ?>-title"
                        class="flex-1 inline-flex items-center gap-2.5 text-slate-800 text-xl sm:text-2xl font-semibold font-['Montserrat'] leading-6">
                        <img
                            src="/assets/Icons/History/time-icon.svg"
                            alt="Time icon"
                            class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0"
                            loading="lazy"
                        >
                        <span><?= htmlspecialchars($event->startTimeIso) ?></span>
                    </h4>
                    <?php if (!empty($event->labels)): ?>
                        <!-- History: language labels under the title in a single row, smaller -->
                        <div class="w-full inline-flex justify-start items-center gap-1">
                            <?php foreach ($event->labels as $label): ?>
                                <div class="flex justify-start items-start gap-1.5">
                                    <span class="px-1 py-1 bg-pink-700/90 rounded-[4px] flex justify-start items-center gap-1.5">
                                        <span class="text-white text-[12px] sm:text-[11px] font-normal font-['Montserrat'] leading-3 tracking-tight">
                                            <?= htmlspecialchars($label) ?>
                                        </span>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- Default: plain text title -->
                    <h4 id="<?= $eventId ?>-title"
                        class="flex-1 text-slate-800 text-xl sm:text-2xl font-semibold font-['Montserrat'] leading-6">
                        <?= htmlspecialchars($event->title) ?>
                    </h4>
                <?php endif; ?>
            </div>

            <?php if (!$isHistoryEvent && !empty($event->labels)): ?>
                <!-- Non-history: labels stacked in the top-right corner -->
                <ul class="inline-flex flex-col justify-center items-end gap-1" aria-label="Event labels">
                    <?php foreach ($event->labels as $label): ?>
                        <li class="inline-flex justify-start items-start gap-2.5">
                            <span class="px-2.5 py-[5px] bg-pink-700 rounded-[10px] flex justify-start items-center gap-2">
                                <span class="text-white text-base font-normal font-['Montserrat'] leading-6">
                                    <?= htmlspecialchars($label) ?>
                                </span>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- Event Details (Location, Date, Time / Group ticket info for history) -->
        <dl class="w-full inline-flex justify-start items-center gap-2.5">
            <div class="flex-1 inline-flex flex-col justify-center items-start gap-2.5">
                <!-- Location -->
                <?php if (!empty($event->locationName)): ?>
                    <div class="inline-flex justify-start items-center gap-[5px]">
                        <dt class="sr-only">Location</dt>
                        <dd class="inline-flex items-center gap-[5px]">
                            <svg class="w-4 h-4 text-slate-800 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                 aria-hidden="true" focusable="false">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <span class="text-slate-800 text-base sm:text-lg font-light font-['Montserrat'] leading-4"><?= htmlspecialchars($event->locationDisplay) ?></span>
                        </dd>
                    </div>
                <?php endif; ?>

                <!-- Date -->
                <div class="inline-flex justify-start items-center gap-[5px]">
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
                        <time datetime="<?= htmlspecialchars($event->isoDate) ?>"
                              class="text-slate-800 text-base sm:text-lg font-light font-['Montserrat'] leading-4"><?= htmlspecialchars($event->dateDisplay) ?></time>
                    </dd>
                </div>

                <!-- Third row: Time (default) or group ticket info (history) -->
                <?php if ($isHistoryEvent): ?>
                    <!-- History: group ticket value instead of time, keeping the same structural row -->
                    <div class="inline-flex justify-start items-center gap-[5px]">
                        <dt class="sr-only">Group ticket</dt>
                        <dd class="inline-flex items-center gap-[5px]">
                            <img
                                src="/assets/Icons/History/price-icon.svg"
                                alt="Group ticket icon"
                                class="w-4 h-4 flex-shrink-0"
                                loading="lazy"
                            >
                            <span class="text-slate-800 text-base sm:text-lg font-light font-['Montserrat'] leading-4">
                                <?= htmlspecialchars($event->historyTicketLabel ?? '') ?>
                            </span>
                        </dd>
                    </div>
                <?php else: ?>
                    <!-- Default: time row used by jazz/storytelling/etc. -->
                    <div class="inline-flex justify-start items-center gap-[5px]">
                        <dt class="sr-only">Time</dt>
                        <dd class="inline-flex items-center gap-[5px]">
                            <svg class="w-4 h-4 text-slate-800 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                 aria-hidden="true" focusable="false">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <span class="text-slate-800 text-base sm:text-lg font-light font-['Montserrat'] leading-4">
                                <time datetime="<?= htmlspecialchars($event->startTimeIso) ?>"><?= htmlspecialchars(explode(' - ', $event->timeDisplay)[0]) ?></time><?php if (!empty($event->endTimeIso)): ?> -
                                    <time
                                        datetime="<?= htmlspecialchars($event->endTimeIso) ?>"><?= htmlspecialchars(explode(' - ', $event->timeDisplay)[1] ?? '') ?></time><?php endif; ?>
                            </span>
                        </dd>
                    </div>
                <?php endif; ?>

                <!-- Price & CTA Row -->
                <div class="w-full inline-flex flex-col justify-start items-start gap-2.5">
                    <div class="w-full h-px bg-gray-200" aria-hidden="true"></div>
                    <div class="w-full inline-flex justify-between items-center">
                        <span class="text-center text-slate-800 text-base sm:text-lg font-normal font-['Montserrat']">

                            <?= htmlspecialchars($event->priceDisplay) ?>
                        </span>
                        <?php if ($isJazzEvent): ?>
                            <button
                                type="button"
                                data-event-session-id="<?= htmlspecialchars((string)$event->eventSessionId) ?>"
                                data-price="<?= htmlspecialchars($event->priceDisplay) ?>"
                                data-is-pay-what-you-like="<?= $event->isPayWhatYouLike ? '1' : '0' ?>"
                                data-confirm-text="<?= htmlspecialchars($event->confirmText) ?>"
                                data-adding-text="<?= htmlspecialchars($event->addingText) ?>"
                                data-success-text="<?= htmlspecialchars($event->successText) ?>"
                                class="px-2.5 py-[5px] bg-slate-800 hover:bg-red rounded-[10px] border border-slate-800 hover:border-red flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-600 focus-visible:ring-offset-2"
                            >
                                <span class="text-center text-stone-100 text-lg sm:text-xl font-normal font-['Montserrat']">
                                    <?= htmlspecialchars($event->ctaLabel) ?>
                                </span>
                            </button>
                        <?php elseif ($isHistoryEvent): ?>
                            <button
                                    type="button"
                                    data-event-session-id="<?= htmlspecialchars((string)$event->eventSessionId) ?>"
                                    data-price="<?= htmlspecialchars($event->priceDisplay) ?>"
                                    data-is-pay-what-you-like="<?= $event->isPayWhatYouLike ? '1' : '0' ?>"
                                    data-confirm-text="<?= htmlspecialchars($event->confirmText) ?>"
                                    data-adding-text="<?= htmlspecialchars($event->addingText) ?>"
                                    data-success-text="<?= htmlspecialchars($event->successText) ?>"
                                    class="px-2.5 py-[5px] bg-slate-800 hover:bg-red rounded-[10px] border border-slate-800 hover:border-red flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-600 focus-visible:ring-offset-2"
                            >
                                <span class="text-center text-stone-100 text-lg sm:text-xl font-normal font-['Montserrat']">
                                    <?= htmlspecialchars($event->ctaLabel) ?>
                                </span>
                            </button>
                        <?php else: ?>
                            <a href="<?= htmlspecialchars($event->ctaUrl) ?>"
                               class="px-2.5 py-[5px] bg-slate-800 hover:bg-red rounded-[10px] border border-slate-800 hover:border-red flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-600 focus-visible:ring-offset-2">
                                <span class="text-center text-stone-100 text-lg sm:text-xl font-normal font-['Montserrat']">
                                    <?= htmlspecialchars($event->ctaLabel) ?>
                                </span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </dl>
    </article>
</li>

