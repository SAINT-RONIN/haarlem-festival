<?php
/**
 * Hero partial - Main hero banner with floating navigation and CTA buttons.
 * Navigation is inside the hero, floating on top of the background image.
 * CTA buttons are positioned at the bottom right.
 *
 * @var string $currentPage Current page identifier for nav highlighting
 */
$currentPage = $currentPage ?? 'home';
?>

<!-- Hero Section with Floating Navigation -->
<div class="self-stretch px-2 pb-2 flex flex-col justify-center items-center gap-5 overflow-hidden">
    <div class="self-stretch h-[calc(100vh-1rem)] bg-black/30 rounded-bl-[30px] rounded-br-[30px] md:rounded-bl-[50px] md:rounded-br-[50px] flex flex-col justify-between items-end overflow-hidden relative" style="background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.5)), url('/assets/Image/HeroImageHome.png') center/cover;">

        <!-- Sticky Navigation - Floating on top of hero image -->
        <div class="w-full px-4 md:px-8 lg:px-24 py-5 flex flex-col justify-center items-end gap-2.5 overflow-visible sticky top-0 z-50">
            <div class="self-stretch bg-slate-800 rounded-2xl flex flex-wrap lg:flex-nowrap justify-between items-center">
                <!-- Logo -->
                <a href="/" class="self-stretch px-4 md:px-5 py-2.5 rounded-2xl flex justify-start items-center gap-2.5">
                    <div class="justify-end text-stone-100 text-2xl md:text-4xl font-medium font-serif-display whitespace-nowrap">Haarlem Festival</div>
                    <div class="w-8 h-8 md:w-10 md:h-10 relative overflow-hidden">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-stone-100 rounded-full flex items-center justify-center">
                            <span class="text-pink-500 text-lg md:text-xl">🌷</span>
                        </div>
                    </div>
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
                    <div class="hidden md:flex justify-start items-center gap-[5px]">
                        <div class="flex justify-start items-center">
                            <span class="text-white text-base font-bold underline cursor-pointer hover:text-pink-300 transition-colors duration-200">EN</span>
                            <span class="text-white text-base font-normal mx-1">/</span>
                            <span class="text-white text-base font-normal cursor-pointer hover:text-pink-300 hover:underline transition-colors duration-200">NL</span>
                        </div>
                    </div>

                    <!-- My Program Button -->
                    <a href="/program" class="w-full lg:w-auto px-3.5 py-2.5 bg-stone-100 hover:bg-pink-700 rounded-[10px] flex justify-center items-center gap-2.5 transition-colors duration-200 group">
                        <svg class="w-5 h-5 group-hover:stroke-white transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span class="text-center text-slate-800 group-hover:text-white text-base font-normal transition-colors duration-200">My Program</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Hero Content - Title and Subtitle -->
        <div class="self-stretch px-4 md:px-12 lg:px-24 flex flex-col justify-center items-start overflow-hidden">
            <h1 class="self-stretch text-white text-4xl sm:text-5xl md:text-7xl lg:text-8xl font-normal leading-tight lg:leading-[110px]">Haarlem Festivals</h1>
            <p class="self-stretch text-white text-xl sm:text-2xl md:text-4xl lg:text-5xl font-light leading-tight lg:leading-[54px]">Four July Days Bringing People Together</p>
        </div>

        <!-- CTA Buttons - Bottom RIGHT -->
        <div class="self-stretch flex flex-col justify-start items-end overflow-hidden">
            <div class="self-stretch h-10 md:h-20"></div>
            <div class="pr-4 md:pr-5 lg:pr-24 pl-4 md:pl-5 py-4 md:py-5 bg-stone-100 rounded-tl-[20px] md:rounded-tl-[35px] flex justify-end items-start gap-3 md:gap-5 overflow-hidden">
                <div class="flex flex-col sm:flex-row justify-start items-center gap-3 md:gap-5">
                    <a href="#events" class="p-2.5 md:p-3.5 bg-pink-700 hover:bg-[#1A2A40] rounded-xl md:rounded-2xl outline outline-1 outline-offset-[-1px] outline-pink-700 hover:outline-[#1A2A40] flex justify-center items-center transition-colors duration-200">
                        <span class="text-center text-stone-100 text-base md:text-xl font-normal">Discover all types of events</span>
                        <div class="px-2 py-1.5 flex justify-center items-center gap-2.5 overflow-hidden">
                            <svg class="w-2 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </a>
                    <a href="#schedule" class="p-2.5 md:p-3.5 bg-stone-100 hover:bg-pink-700 rounded-xl md:rounded-2xl outline outline-2 outline-offset-[-2px] outline-pink-700 flex justify-center items-center transition-colors duration-200 group">
                        <span class="text-center text-pink-700 group-hover:text-stone-100 text-base md:text-xl font-normal transition-colors duration-200">Events schedule</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

