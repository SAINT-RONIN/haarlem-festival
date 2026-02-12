<?php
/**
 * Restaurant Instructions section partial.
 * Shows "How reservations work" with numbered step cards.
 *
 * Restaurant-only section.
 */
?>

<section id="how-it-works" class="self-stretch px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-12 md:py-16 lg:py-20 xl:py-12 flex flex-col justify-start items-center gap-8 sm:gap-10 md:gap-12">
    <!-- Section Title -->
    <h2 class="self-stretch text-center text-slate-800 text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold font-['Montserrat']">
        How reservations work
    </h2>

    <!-- Instruction Cards -->
    <div class="self-stretch flex flex-col lg:flex-row justify-center items-stretch gap-6 sm:gap-8 md:gap-10 lg:gap-16 xl:gap-24">

        <!-- Card 1: Browse -->
        <div class="flex-1 max-w-sm mx-auto lg:mx-0 p-5 bg-white rounded-3xl flex flex-col justify-start items-start gap-3 shadow-sm">
            <div class="self-stretch inline-flex justify-start items-center gap-5">
                <div class="w-11 h-7 bg-slate-800 rounded-full flex justify-center items-center">
                    <span class="text-white text-lg font-bold font-['Montserrat'] leading-5">1</span>
                </div>
            </div>
            <div class="self-stretch flex flex-col justify-center items-center gap-3">
                <div class="w-16 h-14 bg-stone-100 rounded-full flex justify-center items-center">
                    <svg class="w-6 h-6 text-slate-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
                <div class="self-stretch flex flex-col justify-start items-start gap-1.5">
                    <h3 class="self-stretch text-center text-slate-800 text-xl sm:text-2xl font-bold font-['Montserrat']">
                        Browse
                    </h3>
                    <p class="self-stretch text-center text-slate-600 text-sm sm:text-base font-normal font-['Montserrat'] leading-relaxed">
                        Explore participating restaurants and their exclusive festival menus.
                    </p>
                </div>
            </div>
        </div>

        <!-- Card 2: Choose -->
        <div class="flex-1 max-w-sm mx-auto lg:mx-0 p-5 bg-white rounded-3xl flex flex-col justify-start items-start gap-3 shadow-sm">
            <div class="self-stretch inline-flex justify-start items-center gap-5">
                <div class="w-11 h-7 bg-slate-800 rounded-full flex justify-center items-center">
                    <span class="text-white text-lg font-bold font-['Montserrat'] leading-5">2</span>
                </div>
            </div>
            <div class="self-stretch flex flex-col justify-center items-center gap-3">
                <div class="w-16 h-14 bg-stone-100 rounded-full flex justify-center items-center">
                    <svg class="w-6 h-6 text-slate-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <div class="self-stretch flex flex-col justify-start items-start gap-1.5">
                    <h3 class="self-stretch text-center text-slate-800 text-xl sm:text-2xl font-bold font-['Montserrat']">
                        Choose
                    </h3>
                    <p class="self-stretch text-center text-slate-600 text-sm sm:text-base font-normal font-['Montserrat'] leading-relaxed">
                        Pick a date and time slot that fits your schedule.
                    </p>
                </div>
            </div>
        </div>

        <!-- Card 3: Reserve -->
        <div class="flex-1 max-w-sm mx-auto lg:mx-0 p-5 bg-white rounded-3xl flex flex-col justify-start items-start gap-3 shadow-sm">
            <div class="self-stretch inline-flex justify-start items-center gap-5">
                <div class="w-11 h-7 bg-slate-800 rounded-full flex justify-center items-center">
                    <span class="text-white text-lg font-bold font-['Montserrat'] leading-5">3</span>
                </div>
            </div>
            <div class="self-stretch flex flex-col justify-center items-center gap-3">
                <div class="w-16 h-14 bg-stone-100 rounded-full flex justify-center items-center">
                    <svg class="w-6 h-6 text-slate-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20 6 9 17l-5-5"/>
                    </svg>
                </div>
                <div class="self-stretch flex flex-col justify-start items-start gap-1.5">
                    <h3 class="self-stretch text-center text-slate-800 text-xl sm:text-2xl font-bold font-['Montserrat']">
                        Reserve
                    </h3>
                    <p class="self-stretch text-center text-slate-600 text-sm sm:text-base font-normal font-['Montserrat'] leading-relaxed">
                        Complete your booking and receive a confirmation. Done!
                    </p>
                </div>
            </div>
        </div>

    </div>
</section>
