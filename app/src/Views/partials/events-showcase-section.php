<?php
/**
 * Events showcase section partial - Container for event type cards.
 *
 * @var \App\ViewModels\HomeEventTypeViewModel[] $eventTypes
 * @var \App\ViewModels\HomeEventsHeaderViewModel $eventsHeader
 */

use App\Helpers\CmsOutputHelper;
use App\View\ViewRenderer;
?>

<!-- Events Showcase Section -->
<section id="events" class="w-full px-2 sm:px-3 md:px-4 lg:px-8 xl:px-16 2xl:px-24 py-4 sm:py-6 md:py-8 lg:py-12 flex flex-col justify-center items-center" aria-labelledby="events-heading">
    <div class="self-stretch flex flex-col justify-start items-start gap-2 sm:gap-3 md:gap-4 lg:gap-5">
        <header class="self-stretch flex flex-col justify-start items-start">
            <h2 id="events-heading" class="self-stretch text-royal-blue text-xl sm:text-2xl md:text-3xl lg:text-4xl xl:text-5xl 2xl:text-6xl font-bold"><?= CmsOutputHelper::text($eventsHeader->title) ?></h2>
            <p class="self-stretch text-royal-blue text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl font-normal"><?= CmsOutputHelper::text($eventsHeader->subtitle) ?></p>
        </header>

        <?php
        $imageFirst = true;
foreach ($eventTypes as $eventType):
    ?>
            <?php ViewRenderer::render(__DIR__ . '/event-type-card.php', [
                'eventType' => $eventType,
                'imageFirst' => $imageFirst,
            ]); ?>
        <?php
        $imageFirst = !$imageFirst;
endforeach;
?>
    </div>
</section>
