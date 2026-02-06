<?php
/**
 * @var array $locations
 * @var array $cms
 */
$venue = $cms['venue_map_section'] ?? [];
?>

<div id="locations" class="w-full py-8 md:py-12 inline-flex flex-col justify-start items-center gap-2.5 overflow-hidden">
    <div class="hf-container w-full inline-flex flex-col justify-start items-start gap-2.5">

        <h2 class="self-stretch justify-start text-slate-800 text-4xl sm:text-5xl md:text-6xl font-bold leading-tight"><?= htmlspecialchars($venue['venue_main_title']) ?></h2>

        <div class="self-stretch inline-flex flex-col sm:flex-row justify-start items-start sm:items-center gap-3 md:gap-6">
            <div class="justify-start text-slate-800 text-lg md:text-xl font-medium"><?= htmlspecialchars($venue['venue_filter_label']) ?></div>
            <div class="flex flex-wrap justify-start items-center gap-3 md:gap-5">
                <div class="flex justify-start items-center gap-[3px]">
                    <div class="w-2.5 h-2.5 bg-sky-600 rounded-full"></div>
                    <span class="justify-start text-black text-sm md:text-base font-normal leading-normal"><?= htmlspecialchars($venue['venue_filter_jazz']) ?></span>
                </div>
                <div class="flex justify-start items-center gap-[3px]">
                    <div class="w-2.5 h-2.5 bg-orange-800 rounded-full"></div>
                    <span class="justify-start text-black text-sm md:text-base font-normal leading-normal"><?= htmlspecialchars($venue['venue_filter_dance']) ?></span>
                </div>
                <div class="flex justify-start items-center gap-[3px]">
                    <div class="w-2.5 h-2.5 bg-amber-400 rounded-full"></div>
                    <span class="justify-start text-black text-sm md:text-base font-normal leading-normal"><?= htmlspecialchars($venue['venue_filter_history']) ?></span>
                </div>
                <div class="flex justify-start items-center gap-[3px]">
                    <div class="w-2.5 h-2.5 bg-lime-700 rounded-full"></div>
                    <span class="justify-start text-black text-sm md:text-base font-normal leading-normal"><?= htmlspecialchars($venue['venue_filter_restaurants']) ?></span>
                </div>
                <div class="flex justify-start items-center gap-[3px]">
                    <div class="w-2.5 h-2.5 bg-violet-800 rounded-full"></div>
                    <span class="justify-start text-black text-sm md:text-base font-normal leading-normal"><?= htmlspecialchars($venue['venue_filter_stories']) ?></span>
                </div>
            </div>
        </div>

        <!-- Main Container with Locations List and Map -->
        <div class="self-stretch h-auto lg:h-[875px] bg-stone-100 rounded-2xl flex flex-col">

            <!-- Two columns -->
            <div class="flex flex-col lg:flex-row gap-6 lg:gap-12 flex-1 min-h-0">

                <!-- LEFT: Scrollable list -->
                <div class="w-full lg:w-[40%] flex flex-col min-h-0">
                    <ul class="flex-1 min-h-0 overflow-y-auto p-2 space-y-[5px] list-none" >
                        <?php foreach ($locations as $location): ?>
                            <li class="w-full">
                                <?php require __DIR__ . '/location-item.php'; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- RIGHT: Map -->
                <div class="w-full lg:w-[60%] rounded-2xl flex flex-col gap-2.5 min-h-0">

                    <div class="self-stretch px-4 md:px-6 py-2.5 md:py-3.5 bg-white rounded-2xl shadow-[0px_0px_4px_0px_rgba(26,42,64,0.25)] flex flex-col md:flex-row justify-center items-center gap-2.5">
                        <div class="px-4 md:px-6 py-2.5 rounded-2xl flex justify-center items-center gap-2.5">
                            <span class="text-slate-800 text-lg md:text-xl font-bold"><?= htmlspecialchars($venue['venue_filter_title']) ?></span>
                        </div>
                        <div class="px-px flex flex-wrap justify-center items-center gap-2 md:gap-3.5">
                            <button class="h-10 md:h-12 px-4 md:px-6 py-2 md:py-2.5 bg-pink-700 hover:bg-[#1A2A40] rounded-2xl outline outline-1 outline-offset-[-1px] outline-pink-700 hover:outline-[#1A2A40] flex justify-center items-center gap-2.5 transition-colors duration-200">
                                <span class="text-white text-base md:text-lg font-normal"><?= htmlspecialchars($venue['venue_filter_all']) ?></span>
                            </button>
                            <button class="h-10 md:h-12 px-4 md:px-6 py-2 md:py-2.5 bg-white hover:bg-slate-800 rounded-2xl outline outline-1 outline-offset-[-1px] outline-slate-800 hover:outline-[#F5F1EB] flex justify-center items-center gap-2.5 transition-colors duration-200 group">
                                <span class="text-slate-800 group-hover:text-white text-base md:text-lg font-normal transition-colors duration-200"><?= htmlspecialchars($venue['venue_filter_jazz']) ?></span>
                            </button>
                            <button class="h-10 md:h-12 px-4 md:px-6 py-2 md:py-2.5 bg-white hover:bg-slate-800 rounded-2xl outline outline-1 outline-offset-[-1px] outline-slate-800 hover:outline-[#F5F1EB] flex justify-center items-center gap-2.5 transition-colors duration-200 group">
                                <span class="text-slate-800 group-hover:text-white text-base md:text-lg font-normal transition-colors duration-200"><?= htmlspecialchars($venue['venue_filter_dance']) ?></span>
                            </button>
                            <button class="h-10 md:h-12 px-4 md:px-6 py-2 md:py-2.5 bg-white hover:bg-slate-800 rounded-2xl outline outline-1 outline-offset-[-1px] outline-slate-800 hover:outline-[#F5F1EB] flex justify-center items-center gap-2.5 transition-colors duration-200 group">
                                <span class="text-slate-800 group-hover:text-white text-base md:text-lg font-normal transition-colors duration-200"><?= htmlspecialchars($venue['venue_filter_history']) ?></span>
                            </button>
                            <button class="h-10 md:h-12 px-4 md:px-6 py-2 md:py-2.5 bg-white hover:bg-slate-800 rounded-2xl outline outline-1 outline-offset-[-1px] outline-slate-800 hover:outline-[#F5F1EB] flex justify-center items-center gap-2.5 transition-colors duration-200 group">
                                <span class="text-slate-800 group-hover:text-white text-base md:text-lg font-normal transition-colors duration-200"><?= htmlspecialchars($venue['venue_filter_restaurants']) ?></span>
                            </button>
                            <button class="h-10 md:h-12 px-4 md:px-6 py-2 md:py-2.5 bg-white hover:bg-slate-800 rounded-2xl outline outline-1 outline-offset-[-1px] outline-slate-800 hover:outline-[#F5F1EB] flex justify-center items-center gap-2.5 transition-colors duration-200 group">
                                <span class="text-slate-800 group-hover:text-white text-base md:text-lg font-normal transition-colors duration-200"><?= htmlspecialchars($venue['venue_filter_stories']) ?></span>
                            </button>
                        </div>
                    </div>

                    <div class="flex-1 min-h-[300px] shadow-[0px_0px_4px_0px_rgba(0,0,0,0.25)] flex">
                        <div class="flex-1 relative rounded-2xl overflow-hidden">
                            <iframe
                                class="w-full h-full min-h-[300px] lg:min-h-0"
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
</div>
