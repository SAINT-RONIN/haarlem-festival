<?php
/**
 * Renders the hero section for a single historical location page.
 *
 * @var \App\ViewModels\History\HistoricalLocationViewModel $viewModel
 */
use App\Services\SessionService;

$globalUi = $viewModel->globalUi;
$hero = $viewModel->locationHero;
$isLoggedIn = $globalUi->isLoggedIn;
$logoutCsrfToken = $isLoggedIn ? (new SessionService())->getCsrfToken('logout') : null;
?>

<section class="self-stretch px-1 sm:px-2 pb-1 sm:pb-2 flex flex-col justify-center items-center gap-3 sm:gap-5"
         aria-labelledby="history-location-heading">
    <div class="self-stretch min-h-[500px] h-[calc(100vh-0.5rem)] sm:h-[calc(100vh-1rem)]
                rounded-bl-[20px] rounded-br-[20px] sm:rounded-bl-[30px] sm:rounded-br-[30px] md:rounded-bl-[50px] md:rounded-br-[50px]
                flex flex-col justify-between items-end relative hero-background-base"
         style="background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.5)), url('<?= htmlspecialchars($hero->backgroundImageUrl) ?>');"
         role="img" aria-label="<?= htmlspecialchars($hero->mainTitle) ?> hero background">

        <!-- Sticky Navigation - Floating on top of hero image -->
        <header class="w-full px-2 sm:px-4 md:px-6 lg:px-8 xl:px-16 2xl:px-24 py-2 sm:py-3 md:py-4 flex flex-col justify-center items-end gap-2.5 overflow-visible sticky top-0 z-50">
            <nav class="self-stretch bg-royal-blue rounded-xl sm:rounded-2xl flex flex-wrap xl:flex-nowrap justify-between items-center relative"
                 aria-label="Main navigation">
                <!-- Logo -->
                <a href="/"
                   class="self-stretch px-2 sm:px-3 lg:px-4 py-1.5 sm:py-2 lg:py-2.5 rounded-xl sm:rounded-2xl flex justify-start items-center gap-1.5 sm:gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                    <span class="justify-end text-sand text-sm sm:text-base lg:text-lg xl:text-xl 2xl:text-2xl font-medium font-serif-display whitespace-nowrap"><?= htmlspecialchars($globalUi->siteName) ?></span>
                    <img
                        class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 xl:w-7 xl:h-7 2xl:w-9 2xl:h-9"
                        src="/assets/Icons/Logo.svg"
                        alt="" role="presentation">
                </a>

                <!-- Mobile Menu Button with Animation -->
                <button id="hero-menu-btn" data-toggle-menu="hero-nav-menu"
                        class="xl:hidden p-2 sm:p-2.5 mr-1.5 sm:mr-2 text-sand relative w-10 h-10 sm:w-11 sm:h-11 flex items-center justify-center focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2 rounded-lg"
                        aria-expanded="false" aria-controls="hero-nav-menu" aria-label="Toggle navigation menu">
                    <span class="sr-only">Toggle menu</span>
                    <div class="w-5 h-5 sm:w-6 sm:h-6 relative" aria-hidden="true">
                        <span id="hero-bar-1"
                              class="absolute left-0 w-full h-0.5 bg-sand rounded transition-all duration-300 ease-in-out top-0"></span>
                        <span id="hero-bar-2"
                              class="absolute left-0 w-full h-0.5 bg-sand rounded transition-all duration-300 ease-in-out top-1/2 -translate-y-1/2"></span>
                        <span id="hero-bar-3"
                              class="absolute left-0 w-full h-0.5 bg-sand rounded transition-all duration-300 ease-in-out bottom-0"></span>
                    </div>
                </button>

                <!-- Navigation Links -->
                <div id="hero-nav-menu" class="hidden xl:flex
                        xl:relative xl:w-auto xl:top-auto xl:right-auto xl:mt-0 xl:rounded-2xl xl:shadow-none xl:opacity-100 xl:translate-y-0
                        absolute top-full right-0 left-0 mt-2 w-full
                        p-2 bg-royal-blue rounded-xl sm:rounded-2xl shadow-lg
                        flex-col xl:flex-row justify-end items-center gap-1.5 xl:gap-2 2xl:gap-3 z-50
                        opacity-0 -translate-y-2 transition-all duration-300 ease-in-out" role="menubar">
                    <?php if (!empty($globalUi->navLinks ?? null)): ?>
                        <?php foreach ($globalUi->navLinks as $link): ?>
                            <a href="<?= htmlspecialchars($link->href) ?>" role="menuitem"
                               class="w-full xl:w-auto px-3 xl:px-3.5 2xl:px-4 py-2 <?= $link->isActive ? 'bg-red' : 'hover:bg-red' ?> rounded-lg flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2"
                                <?= $link->isActive ? 'aria-current="page"' : '' ?>>
                                <span class="text-center text-sand text-sm 2xl:text-base font-normal"><?= htmlspecialchars($link->label) ?></span>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <a href="/" role="menuitem"
                           class="w-full xl:w-auto px-3 xl:px-3.5 2xl:px-4 py-2 hover:bg-red rounded-lg flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                            <span class="text-center text-sand text-sm 2xl:text-base font-normal"><?= htmlspecialchars($globalUi->navHome) ?></span>
                        </a>
                        <a href="/jazz" role="menuitem"
                           class="w-full xl:w-auto px-3 xl:px-3.5 2xl:px-4 py-2 hover:bg-red rounded-lg flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                            <span class="text-center text-sand text-sm 2xl:text-base font-normal"><?= htmlspecialchars($globalUi->navJazz) ?></span>
                        </a>
                        <a href="/dance" role="menuitem"
                           class="w-full xl:w-auto px-3 xl:px-3.5 2xl:px-4 py-2 hover:bg-red rounded-lg flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                            <span class="text-center text-sand text-sm 2xl:text-base font-normal"><?= htmlspecialchars($globalUi->navDance) ?></span>
                        </a>
                        <a href="/history" role="menuitem"
                           class="w-full xl:w-auto px-3 xl:px-3.5 2xl:px-4 py-2 bg-red rounded-lg flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2" aria-current="page">
                            <span class="text-center text-sand text-sm 2xl:text-base font-normal"><?= htmlspecialchars($globalUi->navHistory) ?></span>
                        </a>
                        <a href="/restaurant" role="menuitem"
                           class="w-full xl:w-auto px-3 xl:px-3.5 2xl:px-4 py-2 hover:bg-red rounded-lg flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                            <span class="text-center text-sand text-sm 2xl:text-base font-normal"><?= htmlspecialchars($globalUi->navRestaurant) ?></span>
                        </a>
                        <a href="/storytelling" role="menuitem"
                           class="w-full xl:w-auto px-3 xl:px-3.5 2xl:px-4 py-2 hover:bg-red rounded-lg flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                            <span class="text-center text-sand text-sm 2xl:text-base font-normal"><?= htmlspecialchars($globalUi->navStorytelling) ?></span>
                        </a>
                    <?php endif; ?>

                    <!-- Divider -->
                    <span class="hidden xl:block w-px h-6 bg-sand/30 mx-1 2xl:mx-2" aria-hidden="true"></span>

                    <!-- Language Switcher placeholder (kept simple for now) -->
                    <div class="hidden xl:flex justify-start items-center" role="group" aria-label="Language selection">
                        <div class="inline-flex justify-start items-center gap-1.5 2xl:gap-2">
                            <button type="button"
                                    class="inline-flex justify-start items-center gap-1.5 text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2 rounded"
                                    aria-pressed="true" aria-label="English language selected">
                                <span class="text-sm 2xl:text-base font-bold underline">EN</span>
                            </button>
                            <span class="text-white text-sm 2xl:text-base font-normal mx-0.5" aria-hidden="true">/</span>
                            <button type="button"
                                    class="inline-flex justify-start items-center gap-1.5 text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2 rounded"
                                    aria-pressed="false" aria-label="Switch to Dutch language">
                                <span class="text-sm 2xl:text-base font-normal hover:underline">NL</span>
                            </button>
                        </div>
                    </div>

                    <!-- My Program Button -->
                    <a href="/program"
                       class="w-full xl:w-auto ml-1 2xl:ml-2 px-4 xl:px-5 2xl:px-6 py-2 bg-sand hover:bg-red rounded-lg flex justify-center items-center gap-2 transition-colors duration-200 group focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                        <i data-lucide="shopping-cart"
                           class="w-4 h-4 2xl:w-5 2xl:h-5 text-royal-blue group-hover:text-sand transition-colors duration-200"
                           aria-hidden="true"></i>
                        <span class="text-center text-royal-blue group-hover:text-sand text-sm 2xl:text-base font-normal transition-colors duration-200"><?= htmlspecialchars($globalUi->btnMyProgram) ?></span>
                    </a>

                    <!-- Login/Logout Button -->
                    <?php if ($isLoggedIn): ?>
                        <form action="/logout" method="post" class="w-full xl:w-auto ml-1 2xl:ml-2">
                            <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string)$logoutCsrfToken) ?>">
                            <button type="submit"
                                    class="w-full px-4 xl:px-5 2xl:px-6 py-2 bg-sand hover:bg-red rounded-lg flex justify-center items-center gap-2 transition-colors duration-200 group focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                                <i data-lucide="log-out"
                                   class="w-4 h-4 2xl:w-5 2xl:h-5 text-royal-blue group-hover:text-sand transition-colors duration-200"
                                   aria-hidden="true"></i>
                                <span class="text-center text-royal-blue group-hover:text-sand text-sm 2xl:text-base font-normal transition-colors duration-200"><?= htmlspecialchars($globalUi->logoutLabel) ?></span>
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="/login"
                           class="w-full xl:w-auto ml-1 2xl:ml-2 px-4 xl:px-5 2xl:px-6 py-2 bg-sand hover:bg-red rounded-lg flex justify-center items-center gap-2 transition-colors duration-200 group focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                            <i data-lucide="log-in"
                               class="w-4 h-4 2xl:w-5 2xl:h-5 text-royal-blue group-hover:text-sand transition-colors duration-200"
                               aria-hidden="true"></i>
                            <span class="text-center text-royal-blue group-hover:text-sand text-sm 2xl:text-base font-normal transition-colors duration-200"><?= htmlspecialchars($globalUi->loginLabel) ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </nav>
        </header>

        <!-- Hero Content - Title, Subtitle, and Map image -->
        <div class="self-stretch px-3 sm:px-4 md:px-8 lg:px-16 xl:px-24 flex flex-col justify-center items-start">
            <div class="w-full flex flex-col lg:flex-row justify-center items-center lg:items-center gap-4 md:gap-6 lg:gap-10">
                <div class="flex-1 flex flex-col justify-center items-center gap-2 sm:gap-3">
                    <h1 id="history-location-heading"
                        class="self-stretch text-white text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl 2xl:text-7xl font-normal leading-tight">
                        <?= htmlspecialchars($hero->mainTitle) ?>
                    </h1>
                    <p class="self-stretch text-white text-sm sm:text-base md:text-xl lg:text-2xl xl:text-3xl 2xl:text-4xl font-light leading-snug">
                        <?= htmlspecialchars($hero->subtitle) ?>
                    </p>
                </div>

                <?php if ($hero->mapImageUrl !== ''): ?>
                    <div class="w-3/4 max-w-md md:max-w-lg lg:max-w-xl flex justify-center items-center">
                        <img src="<?= htmlspecialchars($hero->mapImageUrl) ?>"
                             alt="Map showing the location of <?= htmlspecialchars($hero->mainTitle) ?>"
                             class="w-3/4 h-auto  shadow-lg object-cover">
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Back to history CTA -->
        <div class="self-stretch flex flex-col justify-start items-start">
            <div class="self-stretch h-4 sm:h-6 md:h-10 lg:h-16 xl:h-20" aria-hidden="true"></div>
            <div class="pr-2 sm:pr-3 md:pr-4 lg:pr-12 xl:pr-24 pl-2 sm:pl-3 md:pl-4 py-2 sm:py-3 md:py-4 lg:py-5 bg-sand rounded-tr-[12px] sm:rounded-tr-[15px] md:rounded-tr-[25px] lg:rounded-tr-[35px] flex justify-end items-end">
                <a href="<?= htmlspecialchars($hero->buttonLink) ?>"
                   class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 md:px-5 py-2 sm:py-2.5 md:py-3.5
                          bg-royal-blue hover:bg-red rounded-md sm:rounded-lg md:rounded-xl lg:rounded-2xl
                          text-sand text-xs sm:text-sm md:text-base lg:text-lg font-normal whitespace-nowrap
                          transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 md:w-5 md:h-5 flex-shrink-0" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                    <?= htmlspecialchars($hero->buttonText) ?>
                </a>
            </div>
        </div>
    </div>
</section>

<script src="/assets/js/menu-toggle.js"></script>
