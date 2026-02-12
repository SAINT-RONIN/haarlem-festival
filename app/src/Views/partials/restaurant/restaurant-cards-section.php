<?php
/**
 * Restaurant Cards section partial.
 * Displays participating restaurants with filters and card grid.
 *
 * Restaurant-only section.
 */
?>

<section id="restaurants-grid" class="self-stretch px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-12 md:py-16 lg:py-20 xl:py-12 flex flex-col justify-start items-start gap-6 sm:gap-8 md:gap-10">

    <!-- Section Header -->
    <div class="self-stretch flex flex-col justify-start items-start gap-4 sm:gap-6">
        <h2 class="self-stretch text-slate-800 text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold font-['Montserrat'] leading-tight">
            Explore the participant <br/>restaurants
        </h2>
        <p class="self-stretch text-slate-800 text-base sm:text-lg md:text-xl font-normal font-['Montserrat'] leading-relaxed">
            Discover all restaurants participating in Yummy! <br/>Each one offers a special festival menu, unique flavors, and limited time slots throughout the weekend.
        </p>
    </div>

    <!-- Filter Section -->
    <div class="self-stretch p-4 sm:p-6 bg-slate-800 rounded-2xl sm:rounded-3xl flex flex-col sm:flex-row justify-start items-start sm:items-center gap-4 sm:gap-6 overflow-x-auto">
        <div class="flex justify-start items-center gap-2.5">
            <svg class="w-6 h-6 text-white flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <line x1="4" y1="6" x2="20" y2="6"/>
                <line x1="4" y1="12" x2="20" y2="12"/>
                <line x1="4" y1="18" x2="20" y2="18"/>
            </svg>
            <span class="text-white text-lg sm:text-xl font-medium font-['Montserrat'] whitespace-nowrap">Filters</span>
        </div>

        <div class="flex justify-start items-center gap-2 sm:gap-3 overflow-x-auto flex-shrink-0">
            <!-- All (Active) -->
            <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-pink-700 hover:bg-pink-800 rounded-lg sm:rounded-xl text-white text-lg sm:text-xl font-normal font-['Montserrat'] whitespace-nowrap transition-colors">
                All
            </button>

            <!-- Other filters (Inactive) -->
            <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-stone-100 hover:bg-stone-200 rounded-lg sm:rounded-xl text-slate-800 text-lg sm:text-xl font-normal font-['Montserrat'] whitespace-nowrap transition-colors">
                Dutch
            </button>
            <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-stone-100 hover:bg-stone-200 rounded-lg sm:rounded-xl text-slate-800 text-lg sm:text-xl font-normal font-['Montserrat'] whitespace-nowrap transition-colors">
                European
            </button>
            <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-stone-100 hover:bg-stone-200 rounded-lg sm:rounded-xl text-slate-800 text-lg sm:text-xl font-normal font-['Montserrat'] whitespace-nowrap transition-colors">
                French
            </button>
            <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-stone-100 hover:bg-stone-200 rounded-lg sm:rounded-xl text-slate-800 text-lg sm:text-xl font-normal font-['Montserrat'] whitespace-nowrap transition-colors">
                Modern
            </button>
            <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-stone-100 hover:bg-stone-200 rounded-lg sm:rounded-xl text-slate-800 text-lg sm:text-xl font-normal font-['Montserrat'] whitespace-nowrap transition-colors">
                Fish & Seafood
            </button>
            <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-stone-100 hover:bg-stone-200 rounded-lg sm:rounded-xl text-slate-800 text-lg sm:text-xl font-normal font-['Montserrat'] whitespace-nowrap transition-colors">
                Vegetarian
            </button>
        </div>
    </div>

    <!-- Restaurant Cards Grid -->
    <div class="self-stretch grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 md:gap-10">

        <!-- Card 1: Ratatouille -->
        <div class="bg-white rounded-3xl outline outline-2 outline-slate-800 overflow-hidden flex flex-col h-full">
            <img src="https://placehold.co/600x400" alt="Ratatouille" class="w-full h-48 sm:h-60 object-cover p-2.5"/>
            <div class="w-full h-1 bg-stone-300 border-b-2 border-slate-800"></div>
            <div class="flex-1 p-4 sm:p-5 flex flex-col justify-start gap-3 sm:gap-4">
                <div class="flex justify-between items-start gap-4">
                    <h3 class="text-slate-800 text-lg sm:text-xl font-bold font-['Montserrat']">Ratatouille</h3>
                    <div class="flex flex-col items-end gap-2">
                        <div class="flex gap-1">
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        </div>
                        <span class="text-slate-800 text-base font-semibold font-['Montserrat']">€€€</span>
                    </div>
                </div>
                <div class="space-y-2">
                    <p class="text-slate-800 text-sm sm:text-base font-['Montserrat']"><span class="font-bold">Cuisine:</span> French, fish and seafood, European</p>
                    <p class="text-slate-800 text-sm sm:text-base font-['Montserrat']"><span class="font-bold">Address:</span> Spaarne 96, 2011 CL Haarlem</p>
                </div>
                <p class="text-slate-800 text-sm sm:text-base font-normal font-['Montserrat'] leading-relaxed">
                    Refined dining with a warm touch, where seasonal ingredients and creative flavors come together for an elegant experience.
                </p>
                <div class="flex items-center gap-2 text-slate-800 text-sm sm:text-base font-normal font-['Montserrat']">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                    </svg>
                    <span>5 min walk from Patronaat</span>
                </div>
                <div class="flex gap-3 mt-auto pt-4 justify-center w-full">
                    <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-pink-700 hover:bg-pink-800 rounded-2xl text-white text-lg sm:text-xl font-normal font-['Montserrat'] transition-colors">
                        About it
                    </button>
                    <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-pink-700 hover:bg-pink-800 rounded-2xl text-white text-lg sm:text-xl font-normal font-['Montserrat'] transition-colors">
                        Book table
                    </button>
                </div>
            </div>
        </div>

        <!-- Card 2: Urban Frenchy Bistro Toujours -->
        <div class="bg-white rounded-3xl outline outline-2 outline-slate-800 overflow-hidden flex flex-col h-full">
            <img src="https://placehold.co/600x400" alt="Urban Frenchy Bistro Toujours" class="w-full h-48 sm:h-60 object-cover p-2.5"/>
            <div class="w-full h-1 bg-stone-300 border-b-2 border-slate-800"></div>
            <div class="flex-1 p-4 sm:p-5 flex flex-col justify-start gap-3 sm:gap-4">
                <div class="flex justify-between items-start gap-4">
                    <h3 class="text-slate-800 text-lg sm:text-xl font-bold font-['Montserrat']">Urban Frenchy Bistro Toujours</h3>
                    <div class="flex flex-col items-end gap-2">
                        <div class="flex gap-1">
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        </div>
                        <span class="text-slate-800 text-base font-semibold font-['Montserrat']">€€</span>
                    </div>
                </div>
                <div class="space-y-2">
                    <p class="text-slate-800 text-sm sm:text-base font-['Montserrat']"><span class="font-bold">Cuisine:</span> Dutch, fish and seafood, European</p>
                    <p class="text-slate-800 text-sm sm:text-base font-['Montserrat']"><span class="font-bold">Address:</span> Oude Groenmarkt 10-12, 2011 HL, Haarlem</p>
                </div>
                <p class="text-slate-800 text-sm sm:text-base font-normal font-['Montserrat'] leading-relaxed">
                    A cozy city bistro focused on seafood and comforting dishes in a lively central setting.
                </p>
                <div class="flex items-center gap-2 text-slate-800 text-sm sm:text-base font-normal font-['Montserrat']">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                    </svg>
                    <span>2 min walk from Jopenkerk</span>
                </div>
                <div class="flex gap-3 mt-auto pt-4 justify-center w-full">
                    <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-pink-700 hover:bg-pink-800 rounded-2xl text-white text-lg sm:text-xl font-normal font-['Montserrat'] transition-colors">
                        About it
                    </button>
                    <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-pink-700 hover:bg-pink-800 rounded-2xl text-white text-lg sm:text-xl font-normal font-['Montserrat'] transition-colors">
                        Book table
                    </button>
                </div>
            </div>
        </div>

        <!-- Card 3: New Vegas -->
        <div class="bg-white rounded-3xl outline outline-2 outline-slate-800 overflow-hidden flex flex-col h-full">
            <div class="w-full h-48 sm:h-60 bg-gradient-to-br from-pink-100 to-pink-200 p-2.5 flex justify-end items-start">
                <div class="px-3 py-2 bg-stone-100 rounded-lg flex justify-center items-center">
                    <svg class="w-7 h-7 text-slate-800" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                    </svg>
                </div>
            </div>
            <div class="w-full h-1 bg-stone-300 border-b-2 border-slate-800"></div>
            <div class="flex-1 p-4 sm:p-5 flex flex-col justify-start gap-3 sm:gap-4">
                <div class="flex justify-between items-start gap-4">
                    <h3 class="text-slate-800 text-lg sm:text-xl font-bold font-['Montserrat']">New Vegas</h3>
                    <div class="flex flex-col items-end gap-2">
                        <div class="flex gap-1">
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        </div>
                        <span class="text-slate-800 text-base font-semibold font-['Montserrat']">€€</span>
                    </div>
                </div>
                <div class="space-y-2">
                    <p class="text-slate-800 text-sm sm:text-base font-['Montserrat']"><span class="font-bold">Cuisine:</span> Vegan</p>
                    <p class="text-slate-800 text-sm sm:text-base font-['Montserrat']"><span class="font-bold">Address:</span> Koningstraat 5, 2011 TB Haarlem</p>
                </div>
                <p class="text-slate-800 text-sm sm:text-base font-normal font-['Montserrat'] leading-relaxed">
                    A casual spot with an international feel, offering familiar dishes and vegetarian options right in the city center.
                </p>
                <div class="flex items-center gap-2 text-slate-800 text-sm sm:text-base font-normal font-['Montserrat']">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                    </svg>
                    <span>5 min walk from Patronaat</span>
                </div>
                <div class="flex gap-3 mt-auto pt-4 justify-center w-full">
                    <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-pink-700 hover:bg-pink-800 rounded-2xl text-white text-lg sm:text-xl font-normal font-['Montserrat'] transition-colors">
                        About it
                    </button>
                    <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-pink-700 hover:bg-pink-800 rounded-2xl text-white text-lg sm:text-xl font-normal font-['Montserrat'] transition-colors">
                        Book table
                    </button>
                </div>
            </div>
        </div>

        <!-- Card 4: Grand Cafe Brinkman -->
        <div class="bg-white rounded-3xl outline outline-2 outline-slate-800 overflow-hidden flex flex-col h-full">
            <img src="https://placehold.co/600x400" alt="Grand Cafe Brinkman" class="w-full h-48 sm:h-60 object-cover p-2.5"/>
            <div class="w-full h-1 bg-stone-300 border-b-2 border-slate-800"></div>
            <div class="flex-1 p-4 sm:p-5 flex flex-col justify-start gap-3 sm:gap-4">
                <div class="flex justify-between items-start gap-4">
                    <h3 class="text-slate-800 text-lg sm:text-xl font-bold font-['Montserrat']">Grand Cafe Brinkman</h3>
                    <div class="flex flex-col items-end gap-2">
                        <div class="flex gap-1">
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        </div>
                        <span class="text-slate-800 text-base font-semibold font-['Montserrat']">€€</span>
                    </div>
                </div>
                <div class="space-y-2">
                    <p class="text-slate-800 text-sm sm:text-base font-['Montserrat']"><span class="font-bold">Cuisine:</span> Dutch, European, Modern</p>
                    <p class="text-slate-800 text-sm sm:text-base font-['Montserrat']"><span class="font-bold">Address:</span> Grote Markt 13, 2011 RC, Haarlem</p>
                </div>
                <p class="text-slate-800 text-sm sm:text-base font-normal font-['Montserrat'] leading-relaxed">
                    A classic grand café on Haarlem's main square, serving familiar European dishes in the heart of the festival buzz.
                </p>
                <div class="flex items-center gap-2 text-slate-800 text-sm sm:text-base font-normal font-['Montserrat']">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                    </svg>
                    <span>Located directly on Grote Markt</span>
                </div>
                <div class="flex gap-3 mt-auto pt-4 justify-center w-full">
                    <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-pink-700 hover:bg-pink-800 rounded-2xl text-white text-lg sm:text-xl font-normal font-['Montserrat'] transition-colors">
                        About it
                    </button>
                    <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-pink-700 hover:bg-pink-800 rounded-2xl text-white text-lg sm:text-xl font-normal font-['Montserrat'] transition-colors">
                        Book table
                    </button>
                </div>
            </div>
        </div>

        <!-- Card 5: Restaurant ML -->
        <div class="bg-white rounded-3xl outline outline-2 outline-slate-800 overflow-hidden flex flex-col h-full">
            <img src="https://placehold.co/600x400" alt="Restaurant ML" class="w-full h-48 sm:h-60 object-cover p-2.5"/>
            <div class="w-full h-1 bg-stone-300 border-b-2 border-slate-800"></div>
            <div class="flex-1 p-4 sm:p-5 flex flex-col justify-start gap-3 sm:gap-4">
                <div class="flex justify-between items-start gap-4">
                    <h3 class="text-slate-800 text-lg sm:text-xl font-bold font-['Montserrat']">Restaurant ML</h3>
                    <div class="flex flex-col items-end gap-2">
                        <div class="flex gap-1">
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        </div>
                        <span class="text-slate-800 text-base font-semibold font-['Montserrat']">€€€</span>
                    </div>
                </div>
                <div class="space-y-2">
                    <p class="text-slate-800 text-sm sm:text-base font-['Montserrat']"><span class="font-bold">Cuisine:</span> Dutch, fish and seafood, European</p>
                    <p class="text-slate-800 text-sm sm:text-base font-['Montserrat']"><span class="font-bold">Address:</span> Kleine Houtstraat 70, 2011 DR Haarlem</p>
                </div>
                <p class="text-slate-800 text-sm sm:text-base font-normal font-['Montserrat'] leading-relaxed">
                    A modern fine-dining restaurant known for a refined yet welcoming atmosphere.
                </p>
                <div class="flex items-center gap-2 text-slate-800 text-sm sm:text-base font-normal font-['Montserrat']">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                    </svg>
                    <span>12 min walk from Slachthuis</span>
                </div>
                <div class="flex gap-3 mt-auto pt-4 justify-center w-full">
                    <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-pink-700 hover:bg-pink-800 rounded-2xl text-white text-lg sm:text-xl font-normal font-['Montserrat'] transition-colors">
                        About it
                    </button>
                    <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-pink-700 hover:bg-pink-800 rounded-2xl text-white text-lg sm:text-xl font-normal font-['Montserrat'] transition-colors">
                        Book table
                    </button>
                </div>
            </div>
        </div>

        <!-- Card 6: Café de Roemer -->
        <div class="bg-white rounded-3xl outline outline-2 outline-slate-800 overflow-hidden flex flex-col h-full">
            <img src="https://placehold.co/600x400" alt="Café de Roemer" class="w-full h-48 sm:h-60 object-cover p-2.5"/>
            <div class="w-full h-1 bg-stone-300 border-b-2 border-slate-800"></div>
            <div class="flex-1 p-4 sm:p-5 flex flex-col justify-start gap-3 sm:gap-4">
                <div class="flex justify-between items-start gap-4">
                    <h3 class="text-slate-800 text-lg sm:text-xl font-bold font-['Montserrat']">Café de Roemer</h3>
                    <div class="flex flex-col items-end gap-2">
                        <div class="flex gap-1">
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        </div>
                        <span class="text-slate-800 text-base font-semibold font-['Montserrat']">€€€</span>
                    </div>
                </div>
                <div class="space-y-2">
                    <p class="text-slate-800 text-sm sm:text-base font-['Montserrat']"><span class="font-bold">Cuisine:</span> Dutch, fish and seafood, European</p>
                    <p class="text-slate-800 text-sm sm:text-base font-['Montserrat']"><span class="font-bold">Address:</span> Botermarkt 17, 2011 XL Haarlem</p>
                </div>
                <p class="text-slate-800 text-sm sm:text-base font-normal font-['Montserrat'] leading-relaxed">
                    A cozy neighborhood café serving honest food and classic flavors in a relaxed and friendly setting.
                </p>
                <div class="flex items-center gap-2 text-slate-800 text-sm sm:text-base font-normal font-['Montserrat']">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                    </svg>
                    <span>7 min walk from Puncher Comedy Club</span>
                </div>
                <div class="flex gap-3 mt-auto pt-4 justify-center w-full">
                    <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-pink-700 hover:bg-pink-800 rounded-2xl text-white text-lg sm:text-xl font-normal font-['Montserrat'] transition-colors">
                        About it
                    </button>
                    <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-pink-700 hover:bg-pink-800 rounded-2xl text-white text-lg sm:text-xl font-normal font-['Montserrat'] transition-colors">
                        Book table
                    </button>
                </div>
            </div>
        </div>

        <!-- Card 7: Restaurant Fris -->
        <div class="bg-white rounded-3xl outline outline-2 outline-slate-800 overflow-hidden flex flex-col h-full">
            <img src="https://placehold.co/600x400" alt="Restaurant Fris" class="w-full h-48 sm:h-60 object-cover p-2.5"/>
            <div class="w-full h-1 bg-stone-300 border-b-2 border-slate-800"></div>
            <div class="flex-1 p-4 sm:p-5 flex flex-col justify-start gap-3 sm:gap-4">
                <div class="flex justify-between items-start gap-4">
                    <h3 class="text-slate-800 text-lg sm:text-xl font-bold font-['Montserrat']">Restaurant Fris</h3>
                    <div class="flex flex-col items-end gap-2">
                        <div class="flex gap-1">
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        </div>
                        <span class="text-slate-800 text-base font-semibold font-['Montserrat']">€€€</span>
                    </div>
                </div>
                <div class="space-y-2">
                    <p class="text-slate-800 text-sm sm:text-base font-['Montserrat']"><span class="font-bold">Cuisine:</span> Dutch, French, European</p>
                    <p class="text-slate-800 text-sm sm:text-base font-['Montserrat']"><span class="font-bold">Address:</span> Twijnderslaan 7, 2012 BG, Haarlem</p>
                </div>
                <p class="text-slate-800 text-sm sm:text-base font-normal font-['Montserrat'] leading-relaxed">
                    A contemporary restaurant focused on seasonal ingredients, thoughtful cooking, and elegant flavors without the formality.
                </p>
                <div class="flex items-center gap-2 text-slate-800 text-sm sm:text-base font-normal font-['Montserrat']">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                    </svg>
                    <span>10 min walk from Patronaat</span>
                </div>
                <div class="flex gap-3 mt-auto pt-4 justify-center w-full">
                    <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-pink-700 hover:bg-pink-800 rounded-2xl text-white text-lg sm:text-xl font-normal font-['Montserrat'] transition-colors">
                        About it
                    </button>
                    <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-pink-700 hover:bg-pink-800 rounded-2xl text-white text-lg sm:text-xl font-normal font-['Montserrat'] transition-colors">
                        Book table
                    </button>
                </div>
            </div>
        </div>

    </div>

</section>
