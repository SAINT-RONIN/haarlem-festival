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

<?php
/** @var \App\ViewModels\History\HistoryPageViewModel $viewModel */
$route = $viewModel->routeData;
$locations = $route->locations ?? [];
?>
<section id="route" class="w-full px-6 lg:px-24 py-12 flex flex-col justify-start items-start gap-2.5 overflow-hidden">
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
                        title="Interactive map showing Haarlem Festival route locations">
                    </iframe>
                </div>
            </figure>
        </div>
    </div>
</section>

