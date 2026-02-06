<?php
/**
 * Locations section partial - Map placeholder and venues/restaurants list.
 *
 * @var array $locations Array of location data
 * @var array $cms
 */
$venue = $cms['venue_map_section'];
?>

<!-- Locations Section -->
<div id="locations" class="w-full px-2 sm:px-4 md:px-8 lg:px-16 xl:px-24 py-4 sm:py-6 md:py-10 lg:py-12 inline-flex flex-col justify-start items-start gap-2 sm:gap-2.5 overflow-hidden">
    <h2 class="self-stretch justify-start text-slate-800 text-xl sm:text-2xl md:text-4xl lg:text-5xl xl:text-6xl 2xl:text-7xl font-bold"><?= htmlspecialchars($venue['venue_main_title']) ?></h2>

    <!-- Legend -->
    <div class="self-stretch inline-flex flex-col sm:flex-row justify-start items-start sm:items-center gap-2 sm:gap-3 md:gap-6">
        <div class="justify-start text-slate-800 text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl font-medium"><?= htmlspecialchars($venue['venue_filter_label']) ?></div>
        <div class="flex flex-wrap justify-start items-center gap-2 sm:gap-3 md:gap-5">
            <div class="flex justify-start items-center gap-1">
                <div class="w-2 h-2 sm:w-2.5 sm:h-2.5 bg-sky-600 rounded-full"></div>
                <span class="justify-start text-black text-xs sm:text-sm md:text-base font-normal"><?= htmlspecialchars($venue['venue_filter_jazz']) ?></span>
            </div>
            <div class="flex justify-start items-center gap-1">
                <div class="w-2 h-2 sm:w-2.5 sm:h-2.5 bg-orange-800 rounded-full"></div>
                <span class="justify-start text-black text-xs sm:text-sm md:text-base font-normal"><?= htmlspecialchars($venue['venue_filter_dance']) ?></span>
            </div>
            <div class="flex justify-start items-center gap-1">
                <div class="w-2 h-2 sm:w-2.5 sm:h-2.5 bg-amber-400 rounded-full"></div>
                <span class="justify-start text-black text-xs sm:text-sm md:text-base font-normal"><?= htmlspecialchars($venue['venue_filter_history']) ?></span>
            </div>
            <div class="flex justify-start items-center gap-1">
                <div class="w-2 h-2 sm:w-2.5 sm:h-2.5 bg-lime-700 rounded-full"></div>
                <span class="justify-start text-black text-xs sm:text-sm md:text-base font-normal"><?= htmlspecialchars($venue['venue_filter_restaurants']) ?></span>
            </div>
            <div class="flex justify-start items-center gap-1">
                <div class="w-2 h-2 sm:w-2.5 sm:h-2.5 bg-violet-800 rounded-full"></div>
                <span class="justify-start text-black text-xs sm:text-sm md:text-base font-normal"><?= htmlspecialchars($venue['venue_filter_stories']) ?></span>
            </div>
        </div>
    </div>

    <!-- Main Container with Locations List and Map -->
    <div class="self-stretch h-auto lg:h-[700px] xl:h-[800px] 2xl:h-[875px] bg-stone-100 rounded-xl sm:rounded-2xl flex flex-col">

        <!-- Two columns -->
        <div class="flex flex-col lg:flex-row gap-3 sm:gap-4 md:gap-6 lg:gap-8 xl:gap-12 flex-1 min-h-0">

            <!-- LEFT: Scrollable list -->
            <div class="w-full lg:w-[40%] flex flex-col min-h-0">
                <ul class="flex-1 min-h-0 max-h-[300px] sm:max-h-[350px] md:max-h-[400px] lg:max-h-none overflow-y-auto p-1.5 sm:p-2 space-y-1 sm:space-y-[5px] list-none">
                    <?php foreach ($locations as $location): ?>
                        <li class="w-full">
                            <?php require __DIR__ . '/location-item.php'; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- RIGHT: Map -->
            <div class="w-full lg:w-[60%] rounded-xl sm:rounded-2xl flex flex-col gap-1.5 sm:gap-2 md:gap-2.5 min-h-0">

                <!-- Filter Buttons -->
                <div class="self-stretch px-2 sm:px-3 md:px-4 lg:px-6 py-2 sm:py-2.5 md:py-3 lg:py-3.5 bg-white rounded-xl sm:rounded-2xl shadow-[0px_0px_4px_0px_rgba(26,42,64,0.25)] flex flex-col md:flex-row justify-center items-center gap-1.5 sm:gap-2 md:gap-2.5">
                    <div class="px-2 sm:px-3 md:px-4 lg:px-6 py-1.5 sm:py-2 md:py-2.5 rounded-xl sm:rounded-2xl flex justify-center items-center">
                        <span class="text-slate-800 text-sm sm:text-base md:text-lg lg:text-xl font-bold"><?= htmlspecialchars($venue['venue_filter_title']) ?></span>
                    </div>
                    <div class="flex flex-wrap justify-center items-center gap-1 sm:gap-1.5 md:gap-2 lg:gap-3.5">
                        <button class="h-7 sm:h-8 md:h-10 lg:h-12 px-2 sm:px-3 md:px-4 lg:px-6 py-1 sm:py-1.5 md:py-2 lg:py-2.5 bg-pink-700 hover:bg-[#1A2A40] rounded-lg sm:rounded-xl md:rounded-2xl outline outline-1 outline-offset-[-1px] outline-pink-700 hover:outline-[#1A2A40] flex justify-center items-center transition-colors duration-200">
                            <span class="text-white text-xs sm:text-sm md:text-base lg:text-lg font-normal"><?= htmlspecialchars($venue['venue_filter_all']) ?></span>
                        </button>
                        <button class="h-7 sm:h-8 md:h-10 lg:h-12 px-2 sm:px-3 md:px-4 lg:px-6 py-1 sm:py-1.5 md:py-2 lg:py-2.5 bg-white hover:bg-slate-800 rounded-lg sm:rounded-xl md:rounded-2xl outline outline-1 outline-offset-[-1px] outline-slate-800 hover:outline-[#F5F1EB] flex justify-center items-center transition-colors duration-200 group">
                            <span class="text-slate-800 group-hover:text-white text-xs sm:text-sm md:text-base lg:text-lg font-normal transition-colors duration-200"><?= htmlspecialchars($venue['venue_filter_jazz']) ?></span>
                        </button>
                        <button class="h-7 sm:h-8 md:h-10 lg:h-12 px-2 sm:px-3 md:px-4 lg:px-6 py-1 sm:py-1.5 md:py-2 lg:py-2.5 bg-white hover:bg-slate-800 rounded-lg sm:rounded-xl md:rounded-2xl outline outline-1 outline-offset-[-1px] outline-slate-800 hover:outline-[#F5F1EB] flex justify-center items-center transition-colors duration-200 group">
                            <span class="text-slate-800 group-hover:text-white text-xs sm:text-sm md:text-base lg:text-lg font-normal transition-colors duration-200"><?= htmlspecialchars($venue['venue_filter_dance']) ?></span>
                        </button>
                        <button class="h-7 sm:h-8 md:h-10 lg:h-12 px-2 sm:px-3 md:px-4 lg:px-6 py-1 sm:py-1.5 md:py-2 lg:py-2.5 bg-white hover:bg-slate-800 rounded-lg sm:rounded-xl md:rounded-2xl outline outline-1 outline-offset-[-1px] outline-slate-800 hover:outline-[#F5F1EB] flex justify-center items-center transition-colors duration-200 group">
                            <span class="text-slate-800 group-hover:text-white text-xs sm:text-sm md:text-base lg:text-lg font-normal transition-colors duration-200"><?= htmlspecialchars($venue['venue_filter_history']) ?></span>
                        </button>
                        <button class="h-7 sm:h-8 md:h-10 lg:h-12 px-2 sm:px-3 md:px-4 lg:px-6 py-1 sm:py-1.5 md:py-2 lg:py-2.5 bg-white hover:bg-slate-800 rounded-lg sm:rounded-xl md:rounded-2xl outline outline-1 outline-offset-[-1px] outline-slate-800 hover:outline-[#F5F1EB] flex justify-center items-center transition-colors duration-200 group">
                            <span class="text-slate-800 group-hover:text-white text-xs sm:text-sm md:text-base lg:text-lg font-normal transition-colors duration-200"><?= htmlspecialchars($venue['venue_filter_restaurants']) ?></span>
                        </button>
                        <button class="h-7 sm:h-8 md:h-10 lg:h-12 px-2 sm:px-3 md:px-4 lg:px-6 py-1 sm:py-1.5 md:py-2 lg:py-2.5 bg-white hover:bg-slate-800 rounded-lg sm:rounded-xl md:rounded-2xl outline outline-1 outline-offset-[-1px] outline-slate-800 hover:outline-[#F5F1EB] flex justify-center items-center transition-colors duration-200 group">
                            <span class="text-slate-800 group-hover:text-white text-xs sm:text-sm md:text-base lg:text-lg font-normal transition-colors duration-200"><?= htmlspecialchars($venue['venue_filter_stories']) ?></span>
                        </button>
                    </div>
                </div>

                <!-- Map -->
                <div class="flex-1 min-h-[200px] sm:min-h-[250px] md:min-h-[300px] shadow-[0px_0px_4px_0px_rgba(0,0,0,0.25)] flex">
                    <div class="flex-1 relative rounded-xl sm:rounded-2xl overflow-hidden">
                        <iframe
                            class="w-full h-full min-h-[200px] sm:min-h-[250px] md:min-h-[300px] lg:min-h-0"
                            src="https://www.openstreetmap.org/export/embed.html?bbox=4.6200%2C52.3700%2C4.6600%2C52.3900&layer=mapnik&marker=52.3808%2C4.6368"
                            style="border: 0;"
                            loading="lazy"
                            title="Map of Haarlem event locations">
                        </iframe>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
