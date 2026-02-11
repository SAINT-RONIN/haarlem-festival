<?php
/**
 * History page view.
 *
 * Hardcoded layout for the "A Stroll Through History" landing page.
 * Navigation is handled via the shared header/navbar/footer partials.
 */

// Set any view-specific variables before including the header
$currentPage = 'history';
$includeNav = true;

// Include header BEFORE any HTML output so session_start() can send headers safely
require __DIR__ . '/../partials/header.php';
?>

<main class="w-full bg-sand inline-flex flex-col justify-start items-center">
    <!-- Hardcoded History homepage content -->

    <?php require __DIR__ . '/../partials/hero.php'; ?>

    <?php require __DIR__ . '/../partials/sections/gradient-section.php'; ?>

    <?php require __DIR__ . '/../partials/sections/intro-split-section.php'; ?>

        <div class="w-full px-6 lg:px-24 py-12 flex flex-col justify-start items-start gap-2.5 overflow-hidden">
            <div class="self-stretch justify-start text-slate-800 text-5xl font-bold font-['Montserrat'] leading-[62px]">The Route</div>
            <div class="self-stretch p-3.5 bg-stone-100 rounded-2xl flex flex-col justify-start items-start overflow-hidden route">
                <div class="w-full flex flex-col lg:flex-row justify-start items-stretch gap-8 lg:gap-12">
                    <!-- LEFT: locations list -->
                    <div class="flex-1 inline-flex flex-col justify-center items-stretch gap-[5px]">
                        <?php require __DIR__ . '/../partials/history-map-venue.php'; ?>
                        <?php require __DIR__ . '/../partials/history-map-venue.php'; ?>
                        <?php require __DIR__ . '/../partials/history-map-venue.php'; ?>
                        <?php require __DIR__ . '/../partials/history-map-venue.php'; ?>
                        <?php require __DIR__ . '/../partials/history-map-venue.php'; ?>
                        <?php require __DIR__ . '/../partials/history-map-venue.php'; ?>
                        <?php require __DIR__ . '/../partials/history-map-venue.php'; ?>
                        <?php require __DIR__ . '/../partials/history-map-venue.php'; ?>
                        <?php require __DIR__ . '/../partials/history-map-venue.php'; ?>
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
        <div class="w-full px-6 lg:px-24 py-12 flex flex-col justify-start items-start gap-2.5 overflow-hidden">
            <div class="justify-center text-slate-800 text-5xl font-bold font-['Montserrat'] leading-[62px]">Read more about these locations</div>
            <div class="w-full inline-flex justify-start items-stretch gap-12">
                <?php /* Card 1: Grote Markt */ ?>
                <?php require __DIR__ . '/../partials/history-location-card.php'; ?>

                <?php /* Card 2: Amsterdamse Poort */ ?>
                <?php require __DIR__ . '/../partials/history-location-card.php'; ?>

                <?php /* Card 3: Molen De Adriaan */ ?>
                <?php require __DIR__ . '/../partials/history-location-card.php'; ?>
            </div>
        </div>
        <div class="self-stretch px-6 lg:px-24 py-12 flex flex-col justify-center items-center gap-12 overflow-hidden">
            <div class="self-stretch justify-start text-slate-800 text-5xl font-bold font-['Montserrat'] leading-[62px]">
                Your ticket options to join the experience
            </div>
            <div class="self-stretch inline-flex justify-center items-center gap-48">
                <?php require __DIR__ . '/../partials/history-ticket-option.php'; ?>
                <?php require __DIR__ . '/../partials/history-ticket-option.php'; ?>

            </div>
        </div>
        <div class="self-stretch px-6 lg:px-24 py-12 inline-flex flex-col justify-start items-start gap-6 overflow-hidden">
            <div class="self-stretch flex flex-col justify-start items-start gap-6">
                <div class="inline-flex justify-center items-center gap-2.5">
                    <div class="justify-center text-slate-800 text-5xl font-bold font-['Montserrat'] leading-[62px]">
                        Important information about the tour
                    </div>
                </div>
                <div class="self-stretch inline-flex justify-start items-start">
                    <div class="tour-info flex-1 justify-start text-slate-800 text-lg font-normal font-['Montserrat'] leading-8">
                        <ul class="list-disc pl-6 space-y-1">
                            <li><?php require __DIR__ . '/../partials/history-tour-info-item.php'; ?></li>
                            <li><?php require __DIR__ . '/../partials/history-tour-info-item.php'; ?></li>
                            <li><?php require __DIR__ . '/../partials/history-tour-info-item.php'; ?></li>
                            <li><?php require __DIR__ . '/../partials/history-tour-info-item.php'; ?></li>
                            <li><?php require __DIR__ . '/../partials/history-tour-info-item.php'; ?></li>
                            <li><?php require __DIR__ . '/../partials/history-tour-info-item.php'; ?></li>
                            <li><?php require __DIR__ . '/../partials/history-tour-info-item.php'; ?></li>
                            <li><?php require __DIR__ . '/../partials/history-tour-info-item.php'; ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
