<?php
/**
 * Schedule day column partial - Single day column with date header and event cards.
 *
 * @var \App\ViewModels\HomeScheduleDayViewModel $day
 * @var \App\ViewModels\GlobalUiData $globalUi
 */

use App\ViewModels\GlobalUiData;
use App\View\ViewRenderer;

if (!$globalUi instanceof GlobalUiData) {
    return;
}

$dayName = htmlspecialchars($day->dayName);
$dayNumber = htmlspecialchars((string)$day->dayNumber);
$monthShort = htmlspecialchars($day->monthShort);
$isoDate = htmlspecialchars($day->isoDate);
$eventCount = (int)$day->eventCount;
$sessions = $day->sessions;

// Unique ID for accessibility — pre-computed in HomeMapper
$dayId = $day->htmlId;
?>

<article
        class="w-full lg:flex-1 bg-royal-blue rounded-[12px] sm:rounded-[16px] md:rounded-[20px] flex flex-col justify-start items-start overflow-hidden"
        aria-labelledby="<?php echo $dayId; ?>-heading">
    <!-- Day Header -->
    <header class="self-stretch p-3 sm:p-4 md:p-5 bg-royal-blue flex justify-start items-start gap-2 sm:gap-2.5 overflow-hidden">
        <time datetime="<?php echo $isoDate; ?>"
              class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 bg-sand rounded-md sm:rounded-lg flex flex-col justify-center items-center"
              aria-hidden="true">
            <span class="text-royal-blue text-[10px] sm:text-xs font-semibold uppercase leading-tight tracking-wide"><?php echo $monthShort; ?></span>
            <span class="text-royal-blue text-lg sm:text-xl md:text-2xl font-bold leading-tight"><?php echo $dayNumber; ?></span>
        </time>
        <div class="flex-1 flex flex-col justify-start items-start">
            <h3 id="<?php echo $dayId; ?>-heading"
                class="self-stretch justify-start text-sand text-base sm:text-lg md:text-xl font-semibold leading-snug"><?php echo $dayName; ?>
                , <?php echo $monthShort; ?> <?php echo $dayNumber; ?></h3>
            <p class="self-stretch justify-start text-sand text-xs sm:text-sm font-normal leading-tight">
                <span aria-live="polite"><?php echo $eventCount; ?> <?= htmlspecialchars($globalUi->labelEventsCount) ?></span>
            </p>
        </div>
    </header>

    <!-- Events List -->
    <ul class="self-stretch p-3 sm:p-4 md:p-5 bg-royal-blue flex flex-col justify-start items-start gap-1.5 sm:gap-2 md:gap-2.5 overflow-hidden list-none"
        role="list" aria-label="<?php echo $dayName; ?> events">
        <?php if (empty($sessions)): ?>
            <li class="self-stretch p-3 sm:p-4 md:p-5 bg-white rounded-[12px] sm:rounded-[16px] md:rounded-[20px] flex justify-center items-center">
                <p class="text-slate-500 text-xs sm:text-sm"><?= htmlspecialchars($globalUi->labelNoEvents) ?></p>
            </li>
        <?php else: ?>
            <?php foreach ($sessions as $session): ?>
                <?php ViewRenderer::render(__DIR__ . '/schedule-event-card.php', ['session' => $session]); ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</article>
