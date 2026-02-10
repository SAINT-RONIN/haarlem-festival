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

<main class="w-full bg-[#F5F1EB] inline-flex flex-col justify-start items-center">
    <!-- Hardcoded History homepage content -->
    <div class="w-full bg-[#F5F1EB] inline-flex flex-col justify-start items-center overflow-hidden">
        <div class="w-full bg-[#F5F1EB] flex flex-col justify-start items-start overflow-hidden">
            <div class="self-stretch px-2 pb-2 flex flex-col justify-center items-center gap-5 overflow-hidden">
                <div class="self-stretch bg-black/40 rounded-bl-[50px] rounded-br-[50px] flex flex-col justify-between items-end overflow-hidden">
                    <!-- Hero title and subtitle inside hero container, header is already rendered above -->
                    <div class="self-stretch px-6 lg:px-24 flex flex-col justify-center items-start overflow-hidden">
                        <div class="self-stretch justify-center text-Black-&-White-0 text-8xl font-medium font-['Montserrat']">A STROLL THROUGH HISTORY</div>
                        <div class="self-stretch justify-center text-Black-&-White-0 text-4xl font-normal font-['Montserrat']">Explore nine centuries of turbulent history, magnificent architecture, and cultural treasures</div>
                    </div>
                    <div class="self-stretch inline-flex flex-col justify-start items-start overflow-hidden">
                        <div class="self-stretch h-20"></div>
                        <div class="pl-5 pr-6 lg:pr-24 py-5 bg-stone-100 rounded-tl-[35px] inline-flex justify-start items-start gap-5 overflow-hidden">
                            <div class="flex justify-start items-center gap-5">
                                <div data-state="Default" class="p-3.5 bg-pink-700 rounded-2xl outline outline-1 outline-offset-[-1px] outline-pink-700 flex justify-center items-center">
                                    <div class="text-center justify-center text-stone-100 text-xl font-normal font-['Montserrat']">Explore the tour</div>
                                    <div class="px-2 py-1.5 flex justify-center items-center gap-2.5 overflow-hidden">
                                        <div class="w-1.5 h-3 outline outline-1 outline-offset-[-0.50px] outline-stone-100"></div>
                                    </div>
                                </div>
                                <div data-state="Default" class="p-3.5 bg-stone-100 rounded-2xl outline outline-2 outline-offset-[-2px] outline-pink-700 flex justify-center items-center">
                                    <div class="text-center justify-center text-pink-700 text-xl font-normal font-['Montserrat']">Get tickets</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full px-6 lg:px-24 py-12 flex flex-col justify-center items-center gap-2.5 overflow-hidden">
            <div class="w-full max-w-screen-2xl px-6 lg:px-24 py-16 md:py-24 bg-black/70 rounded-3xl flex flex-col justify-center items-start gap-10 md:gap-14 overflow-hidden">
                <div class="self-stretch justify-start text-Black-&-White-0 text-7xl font-bold font-['Montserrat'] leading-[63px]">Every street holds echoes of the past, shaped by the people who once walked there.</div>
                <div class="self-stretch justify-start text-Black-&-White-0 text-4xl font-normal font-['Montserrat']">Where history comes alive through places, paths, and people.</div>
            </div>
        </div>

        <!-- Experience the living history of Haarlem: balanced two-column layout -->
        <section class="w-full px-6 lg:px-24 py-12 flex justify-center items-center overflow-hidden">
            <div class="w-full max-w-screen-2xl flex flex-col lg:flex-row items-stretch gap-8 lg:gap-12">
                <!-- LEFT: text (50%) -->
                <div class="basis-1/2 flex flex-col justify-center items-start gap-5">
                    <h2 class="text-slate-800 text-4xl md:text-5xl lg:text-6xl font-bold font-['Montserrat'] leading-tight">
                        Experience the living history of Haarlem
                    </h2>
                    <p class="text-slate-800 text-base md:text-lg font-normal font-['Montserrat'] leading-7">
                        A Stroll Through History invites visitors to explore the rich past of Haarlem on foot. Guided tours lead participants through historic streets and landmarks, including locations that played an important role in the city’s cultural, social, and architectural development. The walks are offered in Dutch, English, and Chinese and are suitable for a wide audience.
                    </p>
                    <p class="text-slate-800 text-base md:text-lg font-normal font-['Montserrat'] leading-7">
                        The route has been carefully curated and prepared by local historians and guides to ensure an engaging, informative, and memorable experience. By combining historical facts with stories from the past, the event helps visitors better understand how Haarlem grew into the city it is today.
                    </p>
                    <p class="text-slate-800 text-base md:text-lg font-normal font-['Montserrat'] leading-7">
                        Multiple time slots are available throughout the festival, with different ticket options to keep the event accessible for individuals and families. By joining A Stroll Through History, visitors not only discover Haarlem’s landmarks but also connect with the city through the people, places, and moments that shaped it.
                    </p>
                </div>

                <!-- RIGHT: image (50%) -->
                <div class="basis-1/2 flex justify-center items-center">
                    <img
                        class="w-full rounded-3xl object-cover"
                        src="https://placehold.co/835x535"
                        alt="Haarlem historic city view"
                    />
                </div>
            </div>
        </section>

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
                    <div class="flex-1 justify-start text-slate-800 text-lg font-normal font-['Montserrat'] leading-8">
                        Minimum age requirement: 12 years old<br/>
                        No strollers allowed due to the nature of the walking route<br/>
                        Tour duration: Approximately 2.5 hours including 15-minute break<br/>
                        Group ticket is the best value for a group of 4 or for a family<br/>
                        Starting point: Look for the giant flag near Church of St. Bavo at Grote Markt<br/>
                        Group size: Maximum 12 participants per guide<br/>
                        Comfortable walking shoes recommended<br/>
                        Tours run in light rain; severe weather cancellations will be communicated via email
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
