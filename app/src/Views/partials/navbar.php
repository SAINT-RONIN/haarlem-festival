<?php
/**
 * Navbar partial - Standalone navigation bar for non-hero pages.
 *
 * @var string $currentPage Current page identifier for nav highlighting
 * @var bool $isSticky Whether the navbar should be sticky (default true)
 * @var bool $isDark Whether to use dark background style (default true)
 */
$currentPage = $currentPage ?? 'home';
$isSticky = $isSticky ?? true;
$isDark = $isDark ?? true;

$stickyClass = $isSticky ? 'sticky top-0 z-50' : '';
?>

<!-- Navigation -->
<div class="w-full px-4 md:px-8 lg:px-24 py-5 flex flex-col justify-center items-end gap-2.5 overflow-visible <?php echo $stickyClass; ?>">
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
        <button onclick="document.getElementById('nav-menu').classList.toggle('hidden'); document.getElementById('nav-menu').classList.toggle('flex');" class="lg:hidden p-2.5 mr-2 text-stone-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Navigation Links -->
        <div id="nav-menu" class="hidden lg:flex w-full lg:w-auto p-2.5 bg-slate-800 rounded-2xl flex-col lg:flex-row justify-end items-center gap-2 lg:gap-6">
            <a href="/" class="w-full lg:w-auto px-3.5 py-2.5 <?php echo $currentPage === 'home' ? 'bg-pink-700' : ''; ?> rounded-[10px] flex justify-center items-center">
                <span class="text-center text-stone-100 text-base font-normal">Home</span>
            </a>
            <a href="/jazz" class="w-full lg:w-auto px-3.5 py-2.5 <?php echo $currentPage === 'jazz' ? 'bg-pink-700' : ''; ?> rounded-[10px] flex justify-center items-center">
                <span class="text-center text-stone-100 text-base font-normal">Jazz</span>
            </a>
            <a href="/dance" class="w-full lg:w-auto px-3.5 py-2.5 <?php echo $currentPage === 'dance' ? 'bg-pink-700' : ''; ?> rounded-[10px] flex justify-center items-center">
                <span class="text-center text-stone-100 text-base font-normal">Dance</span>
            </a>
            <a href="/history" class="w-full lg:w-auto px-3.5 py-2.5 <?php echo $currentPage === 'history' ? 'bg-pink-700' : ''; ?> rounded-[10px] flex justify-center items-center">
                <span class="text-center text-stone-100 text-base font-normal">History</span>
            </a>
            <a href="/restaurant" class="w-full lg:w-auto px-3.5 py-2.5 <?php echo $currentPage === 'restaurant' ? 'bg-pink-700' : ''; ?> rounded-[10px] flex justify-center items-center">
                <span class="text-center text-stone-100 text-base font-normal">Restaurant</span>
            </a>
            <a href="/storytelling" class="w-full lg:w-auto px-3.5 py-2.5 <?php echo $currentPage === 'storytelling' ? 'bg-pink-700' : ''; ?> rounded-[10px] flex justify-center items-center">
                <span class="text-center text-stone-100 text-base font-normal">Storytelling</span>
            </a>

            <!-- Language Switcher -->
            <div class="hidden md:flex justify-start items-center gap-[5px]">
                <div class="flex justify-start items-center">
                    <span class="text-white text-base font-bold underline">EN</span>
                    <span class="text-white text-base font-normal mx-1">/</span>
                    <span class="text-white text-base font-normal">NL</span>
                </div>
            </div>

            <!-- My Program Button -->
            <a href="/program" class="w-full lg:w-auto px-3.5 py-2.5 bg-stone-100 rounded-[10px] flex justify-center items-center gap-2.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <span class="text-center text-slate-800 text-base font-normal">My Program</span>
            </a>
        </div>
    </div>
</div>

