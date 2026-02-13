<?php
/**
 * Route section partial - Map placeholder .
 *
 * @var array $locations Array of location data
 * @var array $cms
 */

use App\Helpers\CmsOutputHelper;

/** @var \App\ViewModels\History\HistoryPageViewModel $viewModel */

use App\ViewModels\History\RouteData;

/** @var RouteData $route */
$route = $viewModel->routeData;
$locations = $route->locations ?? [];
?>

<div class="w-full px-6 lg:px-24 py-12 flex flex-col justify-start items-start gap-2.5 overflow-hidden">
    <div class="self-stretch justify-start text-slate-800 text-5xl font-bold font-['Montserrat'] leading-[62px]">
        The Route
    </div>
    <div class="self-stretch p-3.5 bg-stone-100 rounded-2xl flex flex-col justify-start items-start overflow-hidden route">
        <div class="w-full flex flex-col lg:flex-row justify-start items-stretch gap-8 lg:gap-12">
            <!-- LEFT: locations list -->
            <div class="flex-1 inline-flex flex-col justify-center items-stretch gap-[5px]">
                <?php foreach ($locations as $location): ?>
                    <div class="self-stretch inline-flex justify-start items-start gap-[5px]">
                        <div class="flex-1 p-3.5 bg-white rounded-[10px] outline outline-[0.50px] outline-offset-[-0.50px] outline-slate-800 inline-flex flex-col justify-start items-start gap-[5px] overflow-hidden">
                            <div class="self-stretch justify-start text-slate-800 text-base font-bold font-['Montserrat'] leading-4">
                                <?= htmlspecialchars($location['name']) ?>
                            </div>
                            <div class="self-stretch justify-start text-slate-800 text-base font-light font-['Montserrat']">
                                <?= htmlspecialchars($location['address']) ?>
                            </div>
                        </div>
                        <div class="w-9 self-stretch p-3.5 rounded-[10px] border-2 border-slate-800/70 <?= htmlspecialchars($location['badgeClass']) ?>"></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- RIGHT: map -->
            <figure class="flex-1 min-h-[200px] sm:min-h-[250px] md:min-h-[300px] shadow-[0px_0px_4px_0px_rgba(0,0,0,0.25)] flex">
                <div class="flex-1 relative rounded-xl sm:rounded-2xl overflow-hidden">
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