<?php
/**
 * Storytelling detail page schedule section.
 * Simplified layout: no filters, no event count — just day groups with event cards.
 *
 * @var \App\ViewModels\Storytelling\StorytellingDetailPageViewModel $viewModel
 */

use App\Helpers\CmsOutputHelper;

$schedule = $viewModel->scheduleSection;

if ($schedule === null || empty($schedule->days)) {
    return;
}
?>

<section id="<?= htmlspecialchars($schedule->sectionId) ?>"
         class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-10 md:py-12
                flex flex-col justify-start items-start gap-5 sm:gap-7 overflow-hidden"
         aria-labelledby="<?= htmlspecialchars($schedule->sectionId) ?>-heading">

    <!-- Header -->
    <div class="w-full flex flex-col justify-start items-start gap-2.5">
        <div class="w-full flex flex-col sm:flex-row justify-start items-start sm:items-baseline gap-2.5 overflow-hidden">
            <h2 id="<?= htmlspecialchars($schedule->sectionId) ?>-heading"
                class="text-royal-blue text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold">
                <?= CmsOutputHelper::text($schedule->title) ?>
            </h2>
            <?php if ($schedule->year): ?>
                <span class="text-royal-blue text-xl sm:text-2xl md:text-3xl font-bold">
                    <?= CmsOutputHelper::text($schedule->year) ?>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Day Groups -->
    <div class="w-full flex flex-col justify-start items-start gap-3.5">
        <?php foreach ($schedule->days as $day): ?>
            <?php if (!$day->isEmpty): ?>
                <div class="w-full p-3.5 bg-[#ECE6DD] rounded-[20px] flex flex-col justify-start items-start gap-2.5">
                    <!-- Day Name -->
                    <div class="w-full px-3.5 rounded-2xl flex justify-start items-start gap-2.5 overflow-hidden">
                        <h3 class="text-royal-blue text-xl sm:text-2xl font-bold leading-6">
                            <?= htmlspecialchars($day->dayName) ?>
                        </h3>
                    </div>

                    <!-- Session Cards -->
                    <div class="w-full flex flex-col justify-start items-start gap-2.5">
                        <?php foreach ($day->events as $event): ?>
                            <article class="w-full p-3.5 bg-white rounded-2xl grid grid-cols-1 md:grid-cols-3 gap-4 overflow-hidden">
                                <!-- Left (col 1-2): Labels, Title, Info -->
                                <div class="md:col-span-2 flex flex-col justify-start items-start gap-2.5 overflow-hidden">
                                    <!-- Labels -->
                                    <?php if (!empty($event->labels)): ?>
                                        <div class="inline-flex flex-wrap justify-start items-center gap-2.5">
                                            <?php foreach ($event->labels as $label): ?>
                                                <span class="px-2.5 py-0.5 bg-pink-700 rounded-[10px] flex justify-start items-center gap-2">
                                                    <span class="text-white text-sm sm:text-base font-normal leading-6">
                                                        <?= htmlspecialchars($label) ?>
                                                    </span>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Title -->
                                    <h4 class="text-royal-blue text-lg sm:text-xl font-semibold leading-5">
                                        <?= htmlspecialchars($event->title) ?>
                                    </h4>

                                    <!-- Location, Date, Time -->
                                    <div class="inline-flex flex-wrap justify-start items-center gap-4 sm:gap-7">
                                        <?php if (!empty($event->locationName)): ?>
                                            <div class="flex justify-start items-center gap-[5px]">
                                                <svg class="w-4 h-4 text-royal-blue flex-shrink-0" viewBox="0 0 24 24" fill="none"
                                                     stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                                     aria-hidden="true">
                                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                    <circle cx="12" cy="10" r="3"></circle>
                                                </svg>
                                                <span class="text-royal-blue text-base sm:text-lg font-normal leading-4">
                                                    <?= htmlspecialchars($event->locationName) ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>

                                        <div class="flex justify-start items-center gap-[5px]">
                                            <svg class="w-4 h-4 text-royal-blue flex-shrink-0" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                                 aria-hidden="true">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                            </svg>
                                            <time datetime="<?= htmlspecialchars($event->isoDate) ?>"
                                                  class="text-royal-blue text-base sm:text-lg font-normal leading-4">
                                                <?= htmlspecialchars($event->dateDisplay) ?>
                                            </time>
                                        </div>

                                        <div class="flex justify-start items-center gap-[5px]">
                                            <svg class="w-4 h-4 text-royal-blue flex-shrink-0" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                                 aria-hidden="true">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12 6 12 12 16 14"></polyline>
                                            </svg>
                                            <span class="text-royal-blue text-base sm:text-lg font-normal leading-4">
                                                <?= htmlspecialchars($event->timeDisplay) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right (col 3): Price & CTA -->
                                <div class="flex flex-col justify-start items-end gap-4 sm:gap-6 overflow-hidden">
                                    <span class="text-right text-royal-blue text-lg sm:text-xl font-normal leading-5">
                                        <?= htmlspecialchars($event->priceDisplay) ?>
                                    </span>
                                    <button type="button"
                                            data-event-session-id="<?= htmlspecialchars((string)$event->eventSessionId) ?>"
                                            data-price="<?= htmlspecialchars($event->priceDisplay) ?>"
                                            class="px-3.5 py-2.5 rounded-[10px] outline outline-2 outline-offset-[-2px] outline-royal-blue
                                                   inline-flex justify-center items-center gap-2.5
                                                   hover:bg-royal-blue hover:text-stone-100 transition-colors duration-200">
                                        <span class="text-royal-blue text-base sm:text-lg font-normal leading-4">
                                            <?= htmlspecialchars($event->ctaLabel) ?>
                                        </span>
                                    </button>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</section>
