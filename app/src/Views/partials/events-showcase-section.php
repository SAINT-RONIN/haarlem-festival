<?php
/**
 * Events showcase section partial - Container for event type cards.
 *
 * @var array $eventTypes Array of event type data
 */
?>

<!-- Events Showcase Section -->
<div id="events" class="w-full px-4 md:px-12 lg:px-24 py-8 md:py-12 flex flex-col justify-center items-center overflow-hidden">
    <div class="self-stretch flex flex-col justify-start items-start gap-4 md:gap-5">
        <div class="self-stretch flex flex-col justify-start items-start">
            <h2 class="self-stretch text-slate-800 text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold">Explore Our Events</h2>
            <p class="self-stretch text-slate-800 text-xl md:text-2xl lg:text-3xl font-normal">Discover what's happening each day</p>
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

