<?php
/**
 * @var string $currentPage
 * @var array $cms
 */
$currentPage = $currentPage ?? 'home';
$hero = $cms['hero_section'];
$global = $cms['global_ui'];
?>

<!-- Hero Section with Floating Navigation -->
<section class="self-stretch px-1 sm:px-2 pb-1 sm:pb-2 flex flex-col justify-center items-center gap-3 sm:gap-5 overflow-hidden" aria-labelledby="hero-heading">
    <div class="self-stretch min-h-[500px] h-[calc(100vh-0.5rem)] sm:h-[calc(100vh-1rem)] rounded-bl-[20px] rounded-br-[20px] sm:rounded-bl-[30px] sm:rounded-br-[30px] md:rounded-bl-[50px] md:rounded-br-[50px] flex flex-col justify-between items-end overflow-hidden relative hero-background" role="img" aria-label="Haarlem Festival hero background">

        <!-- Sticky Navigation - Floating on top of hero image -->
        <header class="w-full px-2 sm:px-4 md:px-6 lg:px-8 xl:px-16 2xl:px-24 py-2 sm:py-3 md:py-4 flex flex-col justify-center items-end gap-2.5 overflow-visible sticky top-0 z-50">
            <nav class="self-stretch bg-slate-800 rounded-xl sm:rounded-2xl flex flex-wrap xl:flex-nowrap justify-between items-center relative" aria-label="Main navigation">
                <!-- Logo -->
                <a href="/" class="self-stretch px-2 sm:px-3 lg:px-4 py-1.5 sm:py-2 lg:py-2.5 rounded-xl sm:rounded-2xl flex justify-start items-center gap-1.5 sm:gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2">
                    <span class="justify-end text-stone-100 text-sm sm:text-base lg:text-lg xl:text-xl 2xl:text-2xl font-medium font-serif-display whitespace-nowrap"><?= htmlspecialchars($global['site_name']) ?></span>
                    <img
                        class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 xl:w-7 xl:h-7 2xl:w-9 2xl:h-9"
                        src="/assets/Icons/Logo.svg"
                        alt="" role="presentation">
                </a>

                    <!-- Mobile Menu Button with Animation -->
                    <button id="hero-menu-btn" onclick="toggleHeroMenu()" class="xl:hidden p-2 sm:p-2.5 mr-1.5 sm:mr-2 text-stone-100 relative w-10 h-10 sm:w-11 sm:h-11 flex items-center justify-center focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2 rounded-lg" aria-expanded="false" aria-controls="hero-nav-menu" aria-label="Toggle navigation menu">
                        <span class="sr-only">Toggle menu</span>
                        <div class="w-5 h-5 sm:w-6 sm:h-6 relative" aria-hidden="true">
                            <span id="hero-bar-1" class="absolute left-0 w-full h-0.5 bg-stone-100 rounded transition-all duration-300 ease-in-out top-0"></span>
                            <span id="hero-bar-2" class="absolute left-0 w-full h-0.5 bg-stone-100 rounded transition-all duration-300 ease-in-out top-1/2 -translate-y-1/2"></span>
                            <span id="hero-bar-3" class="absolute left-0 w-full h-0.5 bg-stone-100 rounded transition-all duration-300 ease-in-out bottom-0"></span>
                        </div>
                    </button>

                    <!-- Navigation Links - Absolute positioned on mobile/tablet with animation -->
                    <div id="hero-nav-menu" class="hidden xl:flex 
                        xl:relative xl:w-auto xl:top-auto xl:right-auto xl:mt-0 xl:rounded-2xl xl:shadow-none xl:opacity-100 xl:translate-y-0
                        absolute top-full right-0 left-0 mt-2 w-full
                        p-2 bg-slate-800 rounded-xl sm:rounded-2xl shadow-lg
                        flex-col xl:flex-row justify-end items-center gap-1.5 xl:gap-2 2xl:gap-3 z-50
                        opacity-0 -translate-y-2 transition-all duration-300 ease-in-out" role="menubar">
                        <a href="/" role="menuitem" class="w-full xl:w-auto px-3 xl:px-3.5 2xl:px-4 py-2 <?php echo $currentPage === 'home' ? 'bg-pink-700' : 'hover:bg-pink-700'; ?> rounded-lg flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2" <?php echo $currentPage === 'home' ? 'aria-current="page"' : ''; ?>>
                            <span class="text-center text-stone-100 text-sm 2xl:text-base font-normal"><?= htmlspecialchars($global['nav_home']) ?></span>
                        </a>
                        <a href="/jazz" role="menuitem" class="w-full xl:w-auto px-3 xl:px-3.5 2xl:px-4 py-2 <?php echo $currentPage === 'jazz' ? 'bg-pink-700' : 'hover:bg-pink-700'; ?> rounded-lg flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2" <?php echo $currentPage === 'jazz' ? 'aria-current="page"' : ''; ?>>
                            <span class="text-center text-stone-100 text-sm 2xl:text-base font-normal"><?= htmlspecialchars($global['nav_jazz']) ?></span>
                        </a>
                        <a href="/dance" role="menuitem" class="w-full xl:w-auto px-3 xl:px-3.5 2xl:px-4 py-2 <?php echo $currentPage === 'dance' ? 'bg-pink-700' : 'hover:bg-pink-700'; ?> rounded-lg flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2" <?php echo $currentPage === 'dance' ? 'aria-current="page"' : ''; ?>>
                            <span class="text-center text-stone-100 text-sm 2xl:text-base font-normal"><?= htmlspecialchars($global['nav_dance']) ?></span>
                        </a>
                        <a href="/history" role="menuitem" class="w-full xl:w-auto px-3 xl:px-3.5 2xl:px-4 py-2 <?php echo $currentPage === 'history' ? 'bg-pink-700' : 'hover:bg-pink-700'; ?> rounded-lg flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2" <?php echo $currentPage === 'history' ? 'aria-current="page"' : ''; ?>>
                            <span class="text-center text-stone-100 text-sm 2xl:text-base font-normal"><?= htmlspecialchars($global['nav_history']) ?></span>
                        </a>
                        <a href="/restaurant" role="menuitem" class="w-full xl:w-auto px-3 xl:px-3.5 2xl:px-4 py-2 <?php echo $currentPage === 'restaurant' ? 'bg-pink-700' : 'hover:bg-pink-700'; ?> rounded-lg flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2" <?php echo $currentPage === 'restaurant' ? 'aria-current="page"' : ''; ?>>
                            <span class="text-center text-stone-100 text-sm 2xl:text-base font-normal"><?= htmlspecialchars($global['nav_restaurant']) ?></span>
                        </a>
                        <a href="/storytelling" role="menuitem" class="w-full xl:w-auto px-3 xl:px-3.5 2xl:px-4 py-2 <?php echo $currentPage === 'storytelling' ? 'bg-pink-700' : 'hover:bg-pink-700'; ?> rounded-lg flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2" <?php echo $currentPage === 'storytelling' ? 'aria-current="page"' : ''; ?>>
                            <span class="text-center text-stone-100 text-sm 2xl:text-base font-normal"><?= htmlspecialchars($global['nav_storytelling']) ?></span>
                        </a>

                        <!-- Divider -->
                        <span class="hidden xl:block w-px h-6 bg-stone-100/30 mx-1 2xl:mx-2" aria-hidden="true"></span>

                        <!-- Language Switcher -->
                        <div class="hidden xl:flex justify-start items-center" role="group" aria-label="Language selection">
                            <div class="inline-flex justify-start items-center gap-1.5 2xl:gap-2">
                                <button type="button" class="inline-flex justify-start items-center gap-1.5 text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2 rounded" aria-pressed="true" aria-label="English language selected">
                                    <span class="inline-flex w-5 h-4 2xl:w-6 2xl:h-4 rounded-[1px] overflow-hidden shadow-[0px_1px_2px_0px_rgba(16,24,40,0.06)] shadow-[0px_1px_3px_0px_rgba(16,24,40,0.10)]" aria-hidden="true">
                                        <svg viewBox="0 0 190 100" class="w-full h-full" aria-hidden="true" focusable="false" preserveAspectRatio="none">
                                            <rect width="190" height="100" fill="#FFFFFF"/>
                                            <g fill="#B22234">
                                                <rect y="0" width="190" height="7.692"/>
                                                <rect y="15.384" width="190" height="7.692"/>
                                                <rect y="30.768" width="190" height="7.692"/>
                                                <rect y="46.152" width="190" height="7.692"/>
                                                <rect y="61.536" width="190" height="7.692"/>
                                                <rect y="76.92" width="190" height="7.692"/>
                                                <rect y="92.304" width="190" height="7.696"/>
                                            </g>
                                            <rect width="76" height="53.846" fill="#3C3B6E"/>
                                            <g fill="#FFFFFF">
                                                <circle cx="7" cy="7" r="2"/>
                                                <circle cx="17" cy="7" r="2"/>
                                                <circle cx="27" cy="7" r="2"/>
                                                <circle cx="37" cy="7" r="2"/>
                                                <circle cx="47" cy="7" r="2"/>
                                                <circle cx="57" cy="7" r="2"/>
                                                <circle cx="67" cy="7" r="2"/>

                                                <circle cx="12" cy="14" r="2"/>
                                                <circle cx="22" cy="14" r="2"/>
                                                <circle cx="32" cy="14" r="2"/>
                                                <circle cx="42" cy="14" r="2"/>
                                                <circle cx="52" cy="14" r="2"/>
                                                <circle cx="62" cy="14" r="2"/>

                                                <circle cx="7" cy="21" r="2"/>
                                                <circle cx="17" cy="21" r="2"/>
                                                <circle cx="27" cy="21" r="2"/>
                                                <circle cx="37" cy="21" r="2"/>
                                                <circle cx="47" cy="21" r="2"/>
                                                <circle cx="57" cy="21" r="2"/>
                                                <circle cx="67" cy="21" r="2"/>

                                                <circle cx="12" cy="28" r="2"/>
                                                <circle cx="22" cy="28" r="2"/>
                                                <circle cx="32" cy="28" r="2"/>
                                                <circle cx="42" cy="28" r="2"/>
                                                <circle cx="52" cy="28" r="2"/>
                                                <circle cx="62" cy="28" r="2"/>

                                                <circle cx="7" cy="35" r="2"/>
                                                <circle cx="17" cy="35" r="2"/>
                                                <circle cx="27" cy="35" r="2"/>
                                                <circle cx="37" cy="35" r="2"/>
                                                <circle cx="47" cy="35" r="2"/>
                                                <circle cx="57" cy="35" r="2"/>
                                                <circle cx="67" cy="35" r="2"/>

                                                <circle cx="12" cy="42" r="2"/>
                                                <circle cx="22" cy="42" r="2"/>
                                                <circle cx="32" cy="42" r="2"/>
                                                <circle cx="42" cy="42" r="2"/>
                                                <circle cx="52" cy="42" r="2"/>
                                                <circle cx="62" cy="42" r="2"/>

                                                <circle cx="7" cy="49" r="2"/>
                                                <circle cx="17" cy="49" r="2"/>
                                                <circle cx="27" cy="49" r="2"/>
                                                <circle cx="37" cy="49" r="2"/>
                                                <circle cx="47" cy="49" r="2"/>
                                                <circle cx="57" cy="49" r="2"/>
                                                <circle cx="67" cy="49" r="2"/>
                                            </g>
                                        </svg>
                                    </span>
                                    <span class="text-sm 2xl:text-base font-bold underline">EN</span>
                                </button>

                                <span class="text-white text-sm 2xl:text-base font-normal mx-0.5" aria-hidden="true">/</span>

                                <button type="button" class="inline-flex justify-start items-center gap-1.5 text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2 rounded" aria-pressed="false" aria-label="Switch to Dutch language">
                                    <span class="inline-flex w-5 h-4 2xl:w-6 2xl:h-4 rounded-[1px] overflow-hidden shadow-[0px_1px_2px_0px_rgba(16,24,40,0.06)] shadow-[0px_1px_3px_0px_rgba(16,24,40,0.10)]" aria-hidden="true">
                                        <svg viewBox="0 0 60 40" class="w-full h-full" aria-hidden="true" focusable="false">
                                            <rect width="60" height="40" fill="#FFFFFF"/>
                                            <rect width="60" height="13.333" y="0" fill="#AE1C28"/>
                                            <rect width="60" height="13.333" y="26.667" fill="#21468B"/>
                                        </svg>
                                    </span>
                                    <span class="text-sm 2xl:text-base font-normal hover:underline">NL</span>
                                </button>
                            </div>
                        </div>

                        <!-- My Program Button -->
                        <a href="/program" class="w-full xl:w-auto ml-1 2xl:ml-2 px-4 xl:px-5 2xl:px-6 py-2 bg-stone-100 hover:bg-pink-700 rounded-lg flex justify-center items-center gap-2 transition-colors duration-200 group focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2">
                            <i data-lucide="shopping-cart" class="w-4 h-4 2xl:w-5 2xl:h-5 text-slate-800 group-hover:text-white transition-colors duration-200" aria-hidden="true"></i>
                            <span class="text-center text-slate-800 group-hover:text-white text-sm 2xl:text-base font-normal transition-colors duration-200"><?= htmlspecialchars($global['btn_my_program']) ?></span>
                        </a>
                    </div>
                </nav>
        </header>

        <!-- Hero Content - Title and Subtitle -->
        <div class="self-stretch px-3 sm:px-4 md:px-8 lg:px-16 xl:px-24 flex flex-col justify-center items-start overflow-hidden">
            <h1 id="hero-heading" class="self-stretch text-white text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl 2xl:text-7xl font-normal leading-tight"><?= htmlspecialchars($hero['hero_main_title']) ?></h1>
            <p class="self-stretch text-white text-sm sm:text-base md:text-xl lg:text-2xl xl:text-3xl 2xl:text-4xl font-light leading-snug"><?= htmlspecialchars($hero['hero_subtitle']) ?></p>
        </div>

        <!-- CTA Buttons - Bottom RIGHT -->
        <div class="self-stretch flex flex-col justify-start items-end overflow-hidden">
            <div class="self-stretch h-4 sm:h-6 md:h-10 lg:h-16 xl:h-20" aria-hidden="true"></div>
            <div class="pr-2 sm:pr-3 md:pr-4 lg:pr-12 xl:pr-24 pl-2 sm:pl-3 md:pl-4 py-2 sm:py-3 md:py-4 lg:py-5 bg-stone-100 rounded-tl-[12px] sm:rounded-tl-[15px] md:rounded-tl-[25px] lg:rounded-tl-[35px] flex justify-end items-start gap-1.5 sm:gap-2 md:gap-3 lg:gap-5 overflow-hidden">
                <div class="flex flex-row justify-start items-center gap-1.5 sm:gap-2 md:gap-3 lg:gap-5" role="group" aria-label="Hero call to action">
                    <a href="#events" class="p-1.5 sm:p-2 md:p-2.5 lg:p-3.5 bg-pink-700 hover:bg-[#1A2A40] rounded-md sm:rounded-lg md:rounded-xl lg:rounded-2xl outline outline-1 outline-offset-[-1px] outline-pink-700 hover:outline-[#1A2A40] flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2">
                        <span class="text-center text-stone-100 text-[10px] sm:text-xs md:text-sm lg:text-base xl:text-lg font-normal whitespace-nowrap"><?= htmlspecialchars($hero['hero_button_primary']) ?></span>
                        <span class="px-1 sm:px-1.5 lg:px-2 py-0.5 sm:py-1 lg:py-1.5 flex justify-center items-center" aria-hidden="true">
                            <svg class="w-1 h-2 sm:w-1.5 sm:h-3 lg:w-2 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </span>
                    </a>
                    <a href="#schedule" class="p-1.5 sm:p-2 md:p-2.5 lg:p-3.5 bg-stone-100 hover:bg-pink-700 rounded-md sm:rounded-lg md:rounded-xl lg:rounded-2xl outline outline-1 sm:outline-2 outline-offset-[-1px] sm:outline-offset-[-2px] outline-pink-700 flex justify-center items-center transition-colors duration-200 group focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2">
                        <span class="text-center text-pink-700 group-hover:text-stone-100 text-[10px] sm:text-xs md:text-sm lg:text-base xl:text-lg font-normal whitespace-nowrap transition-colors duration-200"><?= htmlspecialchars($hero['hero_button_secondary']) ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="/assets/js/menu-toggle.js"></script>
