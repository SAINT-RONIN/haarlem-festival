<?php
/**
 * @var array $eventTypes
 * @var array $cms
 */
$eventsHeader = $cms['events_overview_header'] ?? [];
?>

<div id="events" class="w-full py-8 md:py-12 flex flex-col justify-center items-center overflow-hidden">
    <div class="hf-container w-full">
        <div class="flex flex-col justify-start items-start gap-4 md:gap-5">
            <div class="self-stretch flex flex-col justify-start items-start">
                <h2 class="self-stretch text-slate-800 text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold"><?= htmlspecialchars($eventsHeader['events_main_title'] ?? 'Explore Our Events') ?></h2>
                <p class="self-stretch text-slate-800 text-xl md:text-2xl lg:text-3xl font-normal"><?= htmlspecialchars($eventsHeader['events_subtitle'] ?? 'Discover what\'s happening each day') ?></p>
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
</div>
