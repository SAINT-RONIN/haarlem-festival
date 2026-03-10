<?php
/**
 * Dance hero section (custom, page-specific).
 */
?>

<section class="w-full bg-black text-sand">
    <div
        class="w-full min-h-[520px] md:min-h-[620px] lg:min-h-[700px] relative flex items-center justify-center overflow-hidden"
        style="background-image:
            linear-gradient(to bottom, rgba(0,0,0,0.4), rgba(0,0,0,0.8)),
            url('/assets/Image/dance-hero.jpg');
            background-position: center;
            background-size: cover;">
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>

        <div class="relative z-10 w-full hf-container flex flex-col gap-6 md:gap-8">
            <div class="max-w-3xl flex flex-col gap-4">
                <p class="text-xs sm:text-sm tracking-[0.3em] uppercase text-red font-semibold">
                    Dance · Haarlem Festival
                </p>
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold font-['Montserrat'] leading-tight">
                    DANCE FESTIVAL 2025
                </h1>
                <p class="text-sm sm:text-base md:text-lg text-sand/90 max-w-xl">
                    Feel the rhythm as beats, lights, and energy collide for a
                    night of non-stop dance, DJs, and unforgettable vibes in Haarlem.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="#dance-schedule"
                   class="inline-flex items-center justify-center px-5 py-2.5 rounded-full bg-red text-sand text-sm md:text-base font-semibold shadow-md hover:bg-red/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2 focus-visible:ring-offset-black transition">
                    Buy Tickets
                </a>
                <a href="#lineup"
                   class="inline-flex items-center justify-center px-5 py-2.5 rounded-full border border-sand/70 text-sand text-sm md:text-base font-semibold hover:bg-sand hover:text-royal-blue focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sand focus-visible:ring-offset-2 focus-visible:ring-offset-black transition">
                    View Lineup
                </a>
            </div>
        </div>
    </div>
</section>