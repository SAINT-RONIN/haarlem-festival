<?php
/**
 * Locations section partial - Map placeholder and venues/restaurants list.
 *
 * @var array $locations Array of location data
 * @var array $cms
 */

use App\Helpers\CmsOutputHelper;

$venue = $cms['venue_map_section'];
?>

<!-- Locations Section -->
<section id="locations" class="w-full px-2 sm:px-4 md:px-8 lg:px-16 xl:px-24 py-4 sm:py-6 md:py-10 lg:py-12 flex flex-col justify-start items-start gap-2 sm:gap-2.5" aria-labelledby="locations-heading">
    <h2 id="locations-heading" class="self-stretch justify-start text-royal-blue text-xl sm:text-2xl md:text-4xl lg:text-5xl xl:text-6xl 2xl:text-7xl font-bold"><?= CmsOutputHelper::text($venue['venue_main_title']) ?></h2>

    <!-- Legend -->
    <div class="self-stretch flex flex-col sm:flex-row justify-start items-start sm:items-center gap-2 sm:gap-3 md:gap-6" role="group" aria-label="Map legend">
        <span class="justify-start text-royal-blue text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl font-medium"><?= CmsOutputHelper::text($venue['venue_filter_label']) ?></span>
        <dl class="flex flex-wrap justify-start items-center gap-2 sm:gap-3 md:gap-5">
            <div class="flex justify-start items-center gap-1">
                <dt class="w-2 h-2 sm:w-2.5 sm:h-2.5 bg-azure-blue rounded-full" aria-hidden="true"></dt>
                <dd class="justify-start text-black text-xs sm:text-sm md:text-base font-normal"><?= CmsOutputHelper::text($venue['venue_filter_jazz']) ?></dd>
            </div>
            <div class="flex justify-start items-center gap-1">
                <dt class="w-2 h-2 sm:w-2.5 sm:h-2.5 bg-deep-crimson rounded-full" aria-hidden="true"></dt>
                <dd class="justify-start text-black text-xs sm:text-sm md:text-base font-normal"><?= CmsOutputHelper::text($venue['venue_filter_dance']) ?></dd>
            </div>
            <div class="flex justify-start items-center gap-1">
                <dt class="w-2 h-2 sm:w-2.5 sm:h-2.5 bg-amber-gold rounded-full" aria-hidden="true"></dt>
                <dd class="justify-start text-black text-xs sm:text-sm md:text-base font-normal"><?= CmsOutputHelper::text($venue['venue_filter_history']) ?></dd>
            </div>
            <div class="flex justify-start items-center gap-1">
                <dt class="w-2 h-2 sm:w-2.5 sm:h-2.5 bg-olive-green rounded-full" aria-hidden="true"></dt>
                <dd class="justify-start text-black text-xs sm:text-sm md:text-base font-normal"><?= CmsOutputHelper::text($venue['venue_filter_restaurants']) ?></dd>
            </div>
            <div class="flex justify-start items-center gap-1">
                <dt class="w-2 h-2 sm:w-2.5 sm:h-2.5 bg-deep-purple rounded-full" aria-hidden="true"></dt>
                <dd class="justify-start text-black text-xs sm:text-sm md:text-base font-normal"><?= CmsOutputHelper::text($venue['venue_filter_stories']) ?></dd>
            </div>
        </dl>
    </div>

    <!-- Main Container with Locations List and Map -->
    <div class="self-stretch h-auto lg:h-[700px] xl:h-[800px] 2xl:h-[875px] bg-sand rounded-xl sm:rounded-2xl flex flex-col">

        <!-- Two columns -->
        <div class="flex flex-col lg:flex-row gap-3 sm:gap-4 md:gap-6 lg:gap-8 xl:gap-12 flex-1 min-h-0">

            <!-- LEFT: Scrollable list -->
            <nav class="w-full lg:w-[40%] flex flex-col min-h-0" aria-label="Festival locations">
                <ul class="flex-1 min-h-0 max-h-[300px] sm:max-h-[350px] md:max-h-[400px] lg:max-h-none overflow-y-auto p-1.5 sm:p-2 space-y-1 sm:space-y-[5px] list-none" role="list">
                    <?php foreach ($locations as $location): ?>
                        <li class="w-full">
                            <?php require __DIR__ . '/location-item.php'; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <!-- RIGHT: Map -->
            <div class="w-full lg:w-[60%] rounded-xl sm:rounded-2xl flex flex-col gap-1.5 sm:gap-2 md:gap-2.5 min-h-0">

                <!-- Filter Buttons -->
                <div class="self-stretch px-2 sm:px-3 md:px-4 lg:px-6 py-2 sm:py-2.5 md:py-3 lg:py-3.5 bg-white rounded-xl sm:rounded-2xl shadow-[0px_0px_4px_0px_rgba(26,42,64,0.25)] flex flex-col md:flex-row justify-center items-center gap-1.5 sm:gap-2 md:gap-2.5" role="group" aria-label="Filter locations by category">
                    <span class="px-2 sm:px-3 md:px-4 lg:px-6 py-1.5 sm:py-2 md:py-2.5 rounded-xl sm:rounded-2xl flex justify-center items-center">
                        <span class="text-royal-blue text-sm sm:text-base md:text-lg lg:text-xl font-bold"><?= CmsOutputHelper::text($venue['venue_filter_title']) ?></span>
                    </span>
                    <div class="flex flex-wrap justify-center items-center gap-1 sm:gap-1.5 md:gap-2 lg:gap-3.5" role="radiogroup" aria-label="Location category filter">
                        <button type="button" class="h-7 sm:h-8 md:h-10 lg:h-12 px-2 sm:px-3 md:px-4 lg:px-6 py-1 sm:py-1.5 md:py-2 lg:py-2.5 bg-red hover:bg-royal-blue rounded-lg sm:rounded-xl md:rounded-2xl outline outline-1 outline-offset-[-1px] outline-red hover:outline-royal-blue flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2" aria-pressed="true">
                            <span class="text-white text-xs sm:text-sm md:text-base lg:text-lg font-normal"><?= CmsOutputHelper::text($venue['venue_filter_all']) ?></span>
                        </button>
                        <button type="button" class="h-7 sm:h-8 md:h-10 lg:h-12 px-2 sm:px-3 md:px-4 lg:px-6 py-1 sm:py-1.5 md:py-2 lg:py-2.5 bg-white hover:bg-royal-blue rounded-lg sm:rounded-xl md:rounded-2xl outline outline-1 outline-offset-[-1px] outline-royal-blue hover:outline-sand flex justify-center items-center transition-colors duration-200 group focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2" aria-pressed="false">
                            <span class="text-royal-blue group-hover:text-white text-xs sm:text-sm md:text-base lg:text-lg font-normal transition-colors duration-200"><?= CmsOutputHelper::text($venue['venue_filter_jazz']) ?></span>
                        </button>
                        <button type="button" class="h-7 sm:h-8 md:h-10 lg:h-12 px-2 sm:px-3 md:px-4 lg:px-6 py-1 sm:py-1.5 md:py-2 lg:py-2.5 bg-white hover:bg-royal-blue rounded-lg sm:rounded-xl md:rounded-2xl outline outline-1 outline-offset-[-1px] outline-royal-blue hover:outline-sand flex justify-center items-center transition-colors duration-200 group focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2" aria-pressed="false">
                            <span class="text-royal-blue group-hover:text-white text-xs sm:text-sm md:text-base lg:text-lg font-normal transition-colors duration-200"><?= CmsOutputHelper::text($venue['venue_filter_dance']) ?></span>
                        </button>
                        <button type="button" class="h-7 sm:h-8 md:h-10 lg:h-12 px-2 sm:px-3 md:px-4 lg:px-6 py-1 sm:py-1.5 md:py-2 lg:py-2.5 bg-white hover:bg-royal-blue rounded-lg sm:rounded-xl md:rounded-2xl outline outline-1 outline-offset-[-1px] outline-royal-blue hover:outline-sand flex justify-center items-center transition-colors duration-200 group focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2" aria-pressed="false">
                            <span class="text-royal-blue group-hover:text-white text-xs sm:text-sm md:text-base lg:text-lg font-normal transition-colors duration-200"><?= CmsOutputHelper::text($venue['venue_filter_history']) ?></span>
                        </button>
                        <button type="button" class="h-7 sm:h-8 md:h-10 lg:h-12 px-2 sm:px-3 md:px-4 lg:px-6 py-1 sm:py-1.5 md:py-2 lg:py-2.5 bg-white hover:bg-royal-blue rounded-lg sm:rounded-xl md:rounded-2xl outline outline-1 outline-offset-[-1px] outline-royal-blue hover:outline-sand flex justify-center items-center transition-colors duration-200 group focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2" aria-pressed="false">
                            <span class="text-royal-blue group-hover:text-white text-xs sm:text-sm md:text-base lg:text-lg font-normal transition-colors duration-200"><?= CmsOutputHelper::text($venue['venue_filter_restaurants']) ?></span>
                        </button>
                        <button type="button" class="h-7 sm:h-8 md:h-10 lg:h-12 px-2 sm:px-3 md:px-4 lg:px-6 py-1 sm:py-1.5 md:py-2 lg:py-2.5 bg-white hover:bg-royal-blue rounded-lg sm:rounded-xl md:rounded-2xl outline outline-1 outline-offset-[-1px] outline-royal-blue hover:outline-sand flex justify-center items-center transition-colors duration-200 group focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2" aria-pressed="false">
                            <span class="text-royal-blue group-hover:text-white text-xs sm:text-sm md:text-base lg:text-lg font-normal transition-colors duration-200"><?= CmsOutputHelper::text($venue['venue_filter_stories']) ?></span>
                        </button>
                    </div>
                </div>

                <!-- Map -->
                <figure class="flex-1 min-h-[200px] sm:min-h-[250px] md:min-h-[300px] shadow-[0px_0px_4px_0px_rgba(0,0,0,0.25)] flex">
                    <div class="flex-1 relative rounded-xl sm:rounded-2xl">
                        <iframe
                            class="w-full h-full min-h-[200px] sm:min-h-[250px] md:min-h-[300px] lg:min-h-0 map-embed-borderless"
                            src="https://www.openstreetmap.org/export/embed.html?bbox=4.6200%2C52.3700%2C4.6600%2C52.3900&layer=mapnik&marker=52.3808%2C4.6368"
                            loading="lazy"
                            title="Interactive map showing Haarlem Festival event locations">
                        </iframe>
                    </div>
                </figure>

            </div>
        </div>
    </div>

</section>
