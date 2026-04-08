<?php
/**
 * Navbar partial - Standalone navigation bar for non-hero pages.
 *
 * @var string $currentPage Current page identifier for nav highlighting
 * @var bool $isSticky Whether the navbar should be sticky (default true)
 * @var bool $isDark Whether to use dark background style (default true)
 * @var bool $isLoggedIn Whether user is logged in (passed from controller/ViewModel)
 */
use App\Services\SessionService;

$currentPage ??= 'home';
$isSticky ??= true;
$isDark ??= true;
$isLoggedIn ??= false;
$logoutCsrfToken = $isLoggedIn ? new SessionService()->getCsrfToken('logout') : null;

$stickyClass = $isSticky ? 'sticky top-0 z-50' : '';
?>

<!-- Navigation -->
<header class="w-full px-2 sm:px-4 md:px-6 lg:px-8 xl:px-16 2xl:px-24 py-2 sm:py-3 md:py-4 flex flex-col justify-center items-end gap-2.5 overflow-visible <?php echo $stickyClass; ?>">
    <nav class="self-stretch bg-royal-blue rounded-xl sm:rounded-2xl flex flex-wrap xl:flex-nowrap justify-between items-center relative"
         aria-label="Main navigation">
        <!-- Logo -->
        <a href="/"
           class="self-stretch px-2 sm:px-3 lg:px-4 py-1.5 sm:py-2 lg:py-2.5 rounded-xl sm:rounded-2xl flex justify-start items-center gap-1.5 sm:gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
            <span class="justify-end text-sand text-sm sm:text-base lg:text-lg xl:text-xl 2xl:text-2xl font-medium font-serif-display whitespace-nowrap">Haarlem Festival</span>
            <img
                    class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 xl:w-7 xl:h-7 2xl:w-9 2xl:h-9"
                    src="/assets/Icons/Logo.svg"
                    alt="" role="presentation">
        </a>

        <!-- Mobile Menu Button -->
        <button type="button" id="nav-menu-btn"
                data-toggle-menu="nav-menu"
                class="xl:hidden p-2 sm:p-2.5 mr-1.5 sm:mr-2 text-sand focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2 rounded-lg"
                aria-expanded="false" aria-controls="nav-menu" aria-label="Toggle navigation menu">
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                 focusable="false">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Navigation Links - Absolute positioned on mobile/tablet -->
        <div id="nav-menu" class="hidden xl:flex 
            xl:relative xl:w-auto xl:top-auto xl:right-auto xl:mt-0 xl:rounded-2xl xl:shadow-none
            absolute top-full right-0 left-0 mt-2 w-full
            p-2 bg-royal-blue rounded-xl sm:rounded-2xl shadow-lg
            flex-col xl:flex-row justify-end items-center gap-1.5 xl:gap-2 2xl:gap-3 z-50" role="menubar">
            <a href="/" role="menuitem"
               class="w-full xl:w-auto px-3 xl:px-3.5 2xl:px-4 py-2 <?php echo $currentPage === 'home' ? 'bg-red' : 'hover:bg-red'; ?> rounded-lg flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2" <?php echo $currentPage === 'home' ? 'aria-current="page"' : ''; ?>>
                <span class="text-center text-sand text-sm 2xl:text-base font-normal">Home</span>
            </a>
            <a href="/jazz" role="menuitem"
               class="w-full xl:w-auto px-3 xl:px-3.5 2xl:px-4 py-2 <?php echo $currentPage === 'jazz' ? 'bg-red' : 'hover:bg-red'; ?> rounded-lg flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2" <?php echo $currentPage === 'jazz' ? 'aria-current="page"' : ''; ?>>
                <span class="text-center text-sand text-sm 2xl:text-base font-normal">Jazz</span>
            </a>
            <a href="/dance" role="menuitem"
               class="w-full xl:w-auto px-3 xl:px-3.5 2xl:px-4 py-2 <?php echo $currentPage === 'dance' ? 'bg-red' : 'hover:bg-red'; ?> rounded-lg flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2" <?php echo $currentPage === 'dance' ? 'aria-current="page"' : ''; ?>>
                <span class="text-center text-sand text-sm 2xl:text-base font-normal">Dance</span>
            </a>
            <a href="/history" role="menuitem"
               class="w-full xl:w-auto px-3 xl:px-3.5 2xl:px-4 py-2 <?php echo $currentPage === 'history' ? 'bg-red' : 'hover:bg-red'; ?> rounded-lg flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2" <?php echo $currentPage === 'history' ? 'aria-current="page"' : ''; ?>>
                <span class="text-center text-sand text-sm 2xl:text-base font-normal">History</span>
            </a>
            <a href="/restaurant" role="menuitem"
               class="w-full xl:w-auto px-3 xl:px-3.5 2xl:px-4 py-2 <?php echo $currentPage === 'restaurant' ? 'bg-red' : 'hover:bg-red'; ?> rounded-lg flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2" <?php echo $currentPage === 'restaurant' ? 'aria-current="page"' : ''; ?>>
                <span class="text-center text-sand text-sm 2xl:text-base font-normal">Restaurant</span>
            </a>
            <a href="/storytelling" role="menuitem"
               class="w-full xl:w-auto px-3 xl:px-3.5 2xl:px-4 py-2 <?php echo $currentPage === 'storytelling' ? 'bg-red' : 'hover:bg-red'; ?> rounded-lg flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2" <?php echo $currentPage === 'storytelling' ? 'aria-current="page"' : ''; ?>>
                <span class="text-center text-sand text-sm 2xl:text-base font-normal">Storytelling</span>
            </a>

            <!-- Divider -->
            <span class="hidden xl:block w-px h-6 bg-sand/30 mx-1 2xl:mx-2" aria-hidden="true"></span>

            <!-- Language Switcher -->
            <div class="hidden xl:flex justify-start items-center" role="group" aria-label="Language selection">
                <div class="inline-flex justify-start items-center gap-1.5 2xl:gap-2">
                    <button type="button"
                            class="inline-flex justify-start items-center gap-1.5 text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2 rounded"
                            aria-pressed="true" aria-label="English language selected">
                        <span class="inline-flex w-5 h-4 2xl:w-6 2xl:h-4 rounded-[1px] shadow-[0px_1px_2px_0px_rgba(16,24,40,0.06)] shadow-[0px_1px_3px_0px_rgba(16,24,40,0.10)]"
                              aria-hidden="true">
                            <svg viewBox="0 0 190 100" class="w-full h-full" aria-hidden="true" focusable="false"
                                 preserveAspectRatio="none">
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

                    <button type="button"
                            class="inline-flex justify-start items-center gap-1.5 text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2 rounded"
                            aria-pressed="false" aria-label="Switch to Dutch language">
                        <span class="inline-flex w-5 h-4 2xl:w-6 2xl:h-4 rounded-[1px] shadow-[0px_1px_2px_0px_rgba(16,24,40,0.06)] shadow-[0px_1px_3px_0px_rgba(16,24,40,0.10)]"
                              aria-hidden="true">
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

            <!-- Login/Logout Button -->
            <!-- My Orders Button (authenticated only) -->
            <?php if ($isLoggedIn): ?>
            <a href="/my-orders" role="menuitem"
               class="w-full xl:w-auto px-4 xl:px-5 2xl:px-6 py-2 bg-sand hover:bg-red rounded-lg flex justify-center items-center gap-2 transition-colors duration-200 group focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                <i data-lucide="receipt" class="w-4 h-4 2xl:w-5 2xl:h-5 text-royal-blue group-hover:text-sand transition-colors duration-200" aria-hidden="true"></i>
                <span class="text-center text-royal-blue group-hover:text-sand text-sm 2xl:text-base font-normal transition-colors duration-200">My Orders</span>
            </a>
            <?php endif; ?>

            <!-- My Program Button -->
            <a href="/my-program"
               class="w-full xl:w-auto px-4 xl:px-5 2xl:px-6 py-2 bg-sand hover:bg-red rounded-lg flex justify-center items-center gap-2 transition-colors duration-200 group focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                <i data-lucide="shopping-cart"
                   class="w-4 h-4 2xl:w-5 2xl:h-5 text-royal-blue group-hover:text-sand transition-colors duration-200"
                   aria-hidden="true"></i>
                <span class="text-center text-royal-blue group-hover:text-sand text-sm 2xl:text-base font-normal transition-colors duration-200">My Program</span>
            </a>

            <!-- Login/Logout Button -->
            <?php if ($isLoggedIn): ?>
                <form action="/logout" method="post" class="w-full xl:w-auto ml-1 2xl:ml-2">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) $logoutCsrfToken) ?>">
                    <button type="submit"
                            class="w-full px-4 xl:px-5 2xl:px-6 py-2 bg-sand hover:bg-red rounded-lg flex justify-center items-center gap-2 transition-colors duration-200 group focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                        <i data-lucide="log-out"
                           class="w-4 h-4 2xl:w-5 2xl:h-5 text-royal-blue group-hover:text-sand transition-colors duration-200"
                           aria-hidden="true"></i>
                        <span class="text-center text-royal-blue group-hover:text-sand text-sm 2xl:text-base font-normal transition-colors duration-200">Logout</span>
                    </button>
                </form>
            <?php else: ?>
                <a href="/login" role="menuitem"
                   class="w-full xl:w-auto ml-1 2xl:ml-2 px-4 xl:px-5 2xl:px-6 py-2 bg-sand hover:bg-red rounded-lg flex justify-center items-center gap-2 transition-colors duration-200 group focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2" <?php echo $currentPage === 'login' ? 'aria-current="page"' : ''; ?>>
                    <i data-lucide="log-in"
                       class="w-4 h-4 2xl:w-5 2xl:h-5 text-royal-blue group-hover:text-sand transition-colors duration-200"
                       aria-hidden="true"></i>
                    <span class="text-center text-royal-blue group-hover:text-sand text-sm 2xl:text-base font-normal transition-colors duration-200">Login</span>
                </a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<script src="/assets/js/menu-toggle.js"></script>
