<?php
/**
 * @var string $currentPage
 * @var array $cms
 */
$currentPage = $currentPage ?? 'home';
$hero = $cms['hero_section'] ?? [];
?>

<!-- Hero Section with Floating Navigation -->
<div class="self-stretch px-2 pb-2 flex flex-col justify-center items-center gap-5 overflow-hidden">
    <div class="self-stretch min-h-[520px] sm:min-h-[640px] lg:min-h-[760px] bg-black/30 rounded-bl-[30px] rounded-br-[30px] md:rounded-bl-[50px] md:rounded-br-[50px] flex flex-col justify-between items-end overflow-hidden relative" style="background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.5)), url('/assets/Image/HeroImageHome.png') center/cover;">

        <!-- Sticky Navigation - Floating on top of hero image -->
        <div class="w-full py-5 flex flex-col justify-center items-end gap-2.5 overflow-visible sticky top-0 z-50">
            <div class="hf-container">
                <div class="bg-slate-800 rounded-2xl flex flex-wrap lg:flex-nowrap justify-between items-center">
                    <!-- Logo -->
                    <a href="/" class="self-stretch px-4 md:px-5 py-2.5 rounded-2xl flex justify-start items-center gap-2.5">
                        <div class="justify-end text-stone-100 text-2xl md:text-4xl font-medium font-serif-display whitespace-nowrap">Haarlem Festival</div>
                        <img
                            class="w-8 h-8 md:w-10 md:h-10"
                            src="/Icons/Logo.svg"
                            alt="Haarlem Festival logo">
                    </a>

                    <!-- Mobile Menu Button -->
                    <button onclick="document.getElementById('hero-nav-menu').classList.toggle('hidden'); document.getElementById('hero-nav-menu').classList.toggle('flex');" class="lg:hidden p-2.5 mr-2 text-stone-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <!-- Navigation Links -->
                    <div id="hero-nav-menu" class="hidden lg:flex w-full lg:w-auto p-2.5 bg-slate-800 rounded-2xl flex-col lg:flex-row justify-end items-center gap-2 lg:gap-6">
                        <a href="/" class="w-full lg:w-auto px-3.5 py-2.5 <?php echo $currentPage === 'home' ? 'bg-pink-700' : 'hover:bg-pink-700'; ?> rounded-[10px] flex justify-center items-center transition-colors duration-200">
                            <span class="text-center text-stone-100 text-base font-normal">Home</span>
                        </a>
                        <a href="/jazz" class="w-full lg:w-auto px-3.5 py-2.5 <?php echo $currentPage === 'jazz' ? 'bg-pink-700' : 'hover:bg-pink-700'; ?> rounded-[10px] flex justify-center items-center transition-colors duration-200">
                            <span class="text-center text-stone-100 text-base font-normal">Jazz</span>
                        </a>
                        <a href="/dance" class="w-full lg:w-auto px-3.5 py-2.5 <?php echo $currentPage === 'dance' ? 'bg-pink-700' : 'hover:bg-pink-700'; ?> rounded-[10px] flex justify-center items-center transition-colors duration-200">
                            <span class="text-center text-stone-100 text-base font-normal">Dance</span>
                        </a>
                        <a href="/history" class="w-full lg:w-auto px-3.5 py-2.5 <?php echo $currentPage === 'history' ? 'bg-pink-700' : 'hover:bg-pink-700'; ?> rounded-[10px] flex justify-center items-center transition-colors duration-200">
                            <span class="text-center text-stone-100 text-base font-normal">History</span>
                        </a>
                        <a href="/restaurant" class="w-full lg:w-auto px-3.5 py-2.5 <?php echo $currentPage === 'restaurant' ? 'bg-pink-700' : 'hover:bg-pink-700'; ?> rounded-[10px] flex justify-center items-center transition-colors duration-200">
                            <span class="text-center text-stone-100 text-base font-normal">Restaurant</span>
                        </a>
                        <a href="/storytelling" class="w-full lg:w-auto px-3.5 py-2.5 <?php echo $currentPage === 'storytelling' ? 'bg-pink-700' : 'hover:bg-pink-700'; ?> rounded-[10px] flex justify-center items-center transition-colors duration-200">
                            <span class="text-center text-stone-100 text-base font-normal">Storytelling</span>
                        </a>

                        <!-- Language Switcher -->
                        <div class="hidden md:flex justify-start items-center gap-2">
                            <div class="inline-flex justify-start items-center gap-[5px]">
                                <button type="button" class="inline-flex justify-start items-center gap-[5px] text-white">
                                    <span class="inline-flex w-6 h-4 rounded-[1px] overflow-hidden shadow-[0px_1px_2px_0px_rgba(16,24,40,0.06)] shadow-[0px_1px_3px_0px_rgba(16,24,40,0.10)]">
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
                                    <span class="text-base font-bold underline">EN</span>
                                </button>

                                <span class="text-white text-base font-normal">/</span>

                                <button type="button" class="inline-flex justify-start items-center gap-[5px] text-white">
                                    <span class="inline-flex w-6 h-4 rounded-[1px] overflow-hidden shadow-[0px_1px_2px_0px_rgba(16,24,40,0.06)] shadow-[0px_1px_3px_0px_rgba(16,24,40,0.10)]">
                                        <svg viewBox="0 0 60 40" class="w-full h-full" aria-hidden="true" focusable="false">
                                            <rect width="60" height="40" fill="#FFFFFF"/>
                                            <rect width="60" height="13.333" y="0" fill="#AE1C28"/>
                                            <rect width="60" height="13.333" y="26.667" fill="#21468B"/>
                                        </svg>
                                    </span>
                                    <span class="text-base font-normal hover:underline">NL</span>
                                </button>
                            </div>
                        </div>

                        <!-- My Program Button -->
                        <a href="/program" class="w-full lg:w-auto px-3.5 py-2.5 bg-stone-100 hover:bg-pink-700 rounded-[10px] flex justify-center items-center gap-2.5 transition-colors duration-200 group">
                            <i data-lucide="shopping-cart" class="w-5 h-5 text-slate-800 group-hover:text-white transition-colors duration-200"></i>
                            <span class="text-center text-slate-800 group-hover:text-white text-base font-normal transition-colors duration-200">My Program</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hero Content -->
        <div class="w-full">
            <div class="hf-container flex flex-col justify-center items-start">
                <h1 class="text-white text-4xl sm:text-5xl md:text-7xl lg:text-8xl font-normal leading-tight lg:leading-[110px]"><?= htmlspecialchars($hero['hero_main_title'] ?? 'Haarlem Festivals') ?></h1>
                <p class="text-white text-xl sm:text-2xl md:text-4xl lg:text-5xl font-light leading-tight lg:leading-[54px]"><?= htmlspecialchars($hero['hero_subtitle'] ?? 'Four July Days Bringing People Together') ?></p>
            </div>
        </div>

        <!-- CTA Buttons -->
        <div class="self-stretch flex flex-col justify-start items-end overflow-hidden">
            <div class="self-stretch h-10 md:h-20"></div>
            <div class="w-full bg-stone-100 rounded-tl-[20px] md:rounded-tl-[35px] overflow-hidden">
                <div class="hf-container py-4 md:py-5 flex justify-end">
                    <div class="flex flex-col sm:flex-row justify-start items-center gap-3 md:gap-5">
                        <a href="#events" class="p-2.5 md:p-3.5 bg-pink-700 hover:bg-[#1A2A40] rounded-xl md:rounded-2xl outline outline-1 outline-offset-[-1px] outline-pink-700 hover:outline-[#1A2A40] flex justify-center items-center transition-colors duration-200">
                            <span class="text-center text-stone-100 text-base md:text-xl font-normal"><?= htmlspecialchars($hero['hero_button_primary'] ?? 'Discover all types of events') ?></span>
                            <div class="px-2 py-1.5 flex justify-center items-center gap-2.5 overflow-hidden">
                                <svg class="w-2 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </a>
                        <a href="#schedule" class="p-2.5 md:p-3.5 bg-stone-100 hover:bg-pink-700 rounded-xl md:rounded-2xl outline outline-2 outline-offset-[-2px] outline-pink-700 flex justify-center items-center transition-colors duration-200 group">
                            <span class="text-center text-pink-700 group-hover:text-stone-100 text-base md:text-xl font-normal transition-colors duration-200"><?= htmlspecialchars($hero['hero_button_secondary'] ?? 'Events schedule') ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
