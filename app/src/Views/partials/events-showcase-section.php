<?php
/**
 * Events showcase section partial - Container for event type cards.
 *
 * @var array $eventTypes Array of event type data
 * @var array $cms
 */
$eventsHeader = $cms['events_overview_header'];
?>

<!-- Events Showcase Section -->
<div id="events" class="w-full px-2 sm:px-3 md:px-4 lg:px-8 xl:px-16 2xl:px-24 py-4 sm:py-6 md:py-8 lg:py-12 flex flex-col justify-center items-center overflow-hidden">
    <div class="self-stretch flex flex-col justify-start items-start gap-2 sm:gap-3 md:gap-4 lg:gap-5">
        <div class="self-stretch flex flex-col justify-start items-start">
            <h2 class="self-stretch text-slate-800 text-xl sm:text-2xl md:text-3xl lg:text-4xl xl:text-5xl 2xl:text-6xl font-bold"><?= htmlspecialchars($eventsHeader['events_main_title']) ?></h2>
            <p class="self-stretch text-slate-800 text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl font-normal"><?= htmlspecialchars($eventsHeader['events_subtitle']) ?></p>
        </div>

        <?php
        $imageFirst = true;
        foreach ($eventTypes as $eventType):
        ?>
            <?php require __DIR__ . '/event-type-card.php'; ?>
        <?php
            $imageFirst = !$imageFirst;
        endforeach;
        ?>
    </div>
</div>
