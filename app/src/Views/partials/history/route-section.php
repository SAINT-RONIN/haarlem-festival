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
        <!-- Two columns -->
        <div class="flex flex-col lg:flex-row items-stretch gap-3 sm:gap-4 md:gap-6 lg:gap-8 xl:gap-12 w-full">
            <!-- LEFT: Scrollable venues list -->
            <nav class="w-full lg:w-[30%] flex flex-col min-h-0 lg:max-h-[650px] lg:h-[650px]" aria-label="History route venues">
                <ul class="flex-1 min-h-0 overflow-y-auto p-1.5 sm:p-2 space-y-1 sm:space-y-[5px] list-none" role="list">
                    <?php foreach ($venues as $index => $venue): ?>
                        <?php /** @var RouteVenue $venue */ ?>
                        <li class="w-full">
                            <?php require __DIR__ . '/route-venue.php'; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <!-- RIGHT: static route map image -->
            <div class="flex-1 lg:flex-[2] flex min-w-0 lg:max-h-[650px] lg:h-[650px]">
                <figure class="flex-1 h-full w-full shadow-[0px_0px_4px_0px_rgba(0,0,0,0.25)] flex">
                    <img
                        src="/assets/Image/History/History-RouteMap.png"
                        alt="Map showing the Haarlem History walking route and venues"
                        title="Haarlem History Route – overview map of all venues"
                        class="w-full h-full max-w-full rounded-2xl object-cover"
                        loading="lazy"
                    >
                </figure>
            </div>
        </div>
    </div>
</section>
