<?php
/**
 * History route section showing the list of route venues and the map.
 *
 * Expects a \App\ViewModels\History\HistoryPageViewModel as $viewModel
 * and uses its routeData property.
 *
 * @var \App\ViewModels\History\HistoryPageViewModel $viewModel
 */

use App\ViewModels\History\RouteData;
use App\ViewModels\History\RouteVenue;

/** @var RouteData $route */
$route = $viewModel->routeData;
$heading = $route->headingText;
$venues  = $route->venues ?? [];
?>
<section id="route" class="w-full px-6 lg:px-24 py-12 flex flex-col justify-start items-start gap-2.5 overflow-hidden">
    <div class="self-stretch justify-start text-slate-800 text-5xl font-bold font-['Montserrat'] leading-[62px]">
        <?= htmlspecialchars($heading) ?>
    </div>
    <div class="self-stretch p-3.5 bg-stone-100 rounded-2xl inline-flex flex-col justify-start items-start overflow-hidden">
        <div class="self-stretch rounded-2xl inline-flex justify-start items-stretch gap-12">
            <!-- LEFT: venues list -->
            <div class="flex-[1] inline-flex flex-col justify-center items-center gap-[5px] min-w-0">
                <?php foreach ($venues as $index => $venue): ?>
                    <?php /** @var RouteVenue $venue */ ?>
                    <?php require __DIR__ . '/route-venue.php'; ?>
                <?php endforeach; ?>
            </div>

            <!-- RIGHT: static route map image, with wider proportion -->
            <div class="flex-[2] flex min-w-0">
                <figure class="flex-1 min-h-[200px] sm:min-h-[250px] md:min-h-[300px] shadow-[0px_0px_4px_0px_rgba(0,0,0,0.25)] flex">
                    <img
                        src="/assets/Image/History/History-RouteMap.png"
                        alt="Map showing the Haarlem History walking route and venues"
                        title="Haarlem History Route – overview map of all venues"
                        class="w-full h-full min-h-[200px] sm:min-h-[250px] md:min-h-[300px] lg:min-h-0 rounded-2xl object-cover"
                        loading="lazy"
                    >
                </figure>
            </div>
        </div>
    </div>
</section>
