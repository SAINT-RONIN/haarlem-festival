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
                <h2 class="self-stretch text-slate-800 text-4xl sm:text-5xl md:text-6xl font-bold leading-tight"><?= htmlspecialchars($eventsHeader['events_main_title']) ?></h2>
                <p class="self-stretch text-slate-800 text-xl md:text-2xl font-normal leading-snug"><?= htmlspecialchars($eventsHeader['events_subtitle']) ?></p>
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
