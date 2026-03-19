<?php
/**
 * Renders the full-screen hero section for a single storytelling event detail page.
 * The reason for this is because the detail page needs a custom hero with an inline
 * navbar, event-specific nav links, and CTA buttons — the shared hero.php partial
 * cannot accommodate this per-event structure.
 *
 * @var \App\ViewModels\Storytelling\StorytellingDetailPageViewModel $viewModel
 */

$globalUi = $viewModel->globalUi;
$hero = $viewModel->detailHero;
$isLoggedIn = $globalUi->isLoggedIn;
?>

<section class="self-stretch px-1 sm:px-2 pb-1 sm:pb-2 flex flex-col justify-center items-center"
         aria-labelledby="story-detail-heading">
    <div class="self-stretch min-h-[500px] h-[calc(100vh-0.5rem)] sm:h-[calc(100vh-1rem)]
                rounded-bl-[20px] rounded-br-[20px] sm:rounded-bl-[30px] sm:rounded-br-[30px] md:rounded-bl-[50px] md:rounded-br-[50px]
                flex flex-col justify-between items-end relative hero-background-base"
         style="background-image: linear-gradient(to bottom, rgba(0,0,0,0.25), rgba(0,0,0,0.65)), url('<?= htmlspecialchars($hero->heroImageUrl) ?>');"
         role="img" aria-label="<?= htmlspecialchars($hero->title) ?> hero background">

        <header class="w-full px-2 sm:px-4 md:px-6 lg:px-8 xl:px-16 2xl:px-24 py-2 sm:py-3 md:py-4
                       flex flex-col justify-center items-end gap-2.5 overflow-visible sticky top-0 z-50">
            <nav class="self-stretch bg-royal-blue rounded-xl sm:rounded-2xl flex flex-wrap xl:flex-nowrap justify-between items-center relative"
                 aria-label="Main navigation">
                <a href="/"
                   class="self-stretch px-2 sm:px-3 lg:px-4 py-1.5 sm:py-2 lg:py-2.5 rounded-xl sm:rounded-2xl flex justify-start items-center gap-1.5 sm:gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                    <span class="justify-end text-sand text-sm sm:text-base lg:text-lg xl:text-xl 2xl:text-2xl font-medium font-serif-display whitespace-nowrap"><?= htmlspecialchars($globalUi->siteName) ?></span>
                    <img class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 xl:w-7 xl:h-7 2xl:w-9 2xl:h-9"
                         src="/assets/Icons/Logo.svg" alt="" role="presentation">
                </a>

                <button type="button" id="detail-menu-btn" data-toggle-menu="detail-nav-menu"
                        class="xl:hidden p-2 sm:p-2.5 mr-1.5 sm:mr-2 text-sand focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2 rounded-lg"
                        aria-expanded="false" aria-controls="detail-nav-menu" aria-label="Toggle navigation menu">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div id="detail-nav-menu"
                     class="hidden xl:flex xl:relative xl:w-auto xl:top-auto xl:right-auto xl:mt-0 xl:rounded-2xl xl:shadow-none
                            absolute top-full right-0 left-0 mt-2 w-full
                            p-2 bg-royal-blue rounded-xl sm:rounded-2xl shadow-lg
                            flex-col xl:flex-row justify-end items-center gap-1.5 xl:gap-2 2xl:gap-3 z-50"
                     role="menubar">
                    <?php foreach ($hero->navLinks as $link): ?>
                        <a href="<?= htmlspecialchars($link->href) ?>" role="menuitem"
                           class="w-full xl:w-auto px-3 xl:px-3.5 2xl:px-4 py-2 <?= $link->isActive ? 'bg-red' : 'hover:bg-red' ?> rounded-lg flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2"
                           <?= $link->isActive ? 'aria-current="page"' : '' ?>>
                            <span class="text-center text-sand text-sm 2xl:text-base font-normal"><?= htmlspecialchars($link->label) ?></span>
                        </a>
                    <?php endforeach; ?>

                    <span class="hidden xl:block w-px h-6 bg-sand/30 mx-1 2xl:mx-2" aria-hidden="true"></span>

                    <a href="/my-program"
                       class="w-full xl:w-auto px-4 xl:px-5 2xl:px-6 py-2 bg-sand hover:bg-red rounded-lg flex justify-center items-center gap-2 transition-colors duration-200 group focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                        <i data-lucide="shopping-cart" class="w-4 h-4 2xl:w-5 2xl:h-5 text-royal-blue group-hover:text-sand transition-colors duration-200" aria-hidden="true"></i>
                        <span class="text-center text-royal-blue group-hover:text-sand text-sm 2xl:text-base font-normal transition-colors duration-200"><?= htmlspecialchars($globalUi->btnMyProgram) ?></span>
                    </a>

                    <?php if ($isLoggedIn): ?>
                        <a href="/logout"
                           class="w-full xl:w-auto ml-1 2xl:ml-2 px-4 xl:px-5 2xl:px-6 py-2 bg-sand hover:bg-red rounded-lg flex justify-center items-center gap-2 transition-colors duration-200 group focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                            <i data-lucide="log-out" class="w-4 h-4 2xl:w-5 2xl:h-5 text-royal-blue group-hover:text-sand transition-colors duration-200" aria-hidden="true"></i>
                            <span class="text-center text-royal-blue group-hover:text-sand text-sm 2xl:text-base font-normal transition-colors duration-200"><?= htmlspecialchars($globalUi->logoutLabel) ?></span>
                        </a>
                    <?php else: ?>
                        <a href="/login"
                           class="w-full xl:w-auto ml-1 2xl:ml-2 px-4 xl:px-5 2xl:px-6 py-2 bg-sand hover:bg-red rounded-lg flex justify-center items-center gap-2 transition-colors duration-200 group focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                            <i data-lucide="log-in" class="w-4 h-4 2xl:w-5 2xl:h-5 text-royal-blue group-hover:text-sand transition-colors duration-200" aria-hidden="true"></i>
                            <span class="text-center text-royal-blue group-hover:text-sand text-sm 2xl:text-base font-normal transition-colors duration-200"><?= htmlspecialchars($globalUi->loginLabel) ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </nav>
        </header>

        <div class="self-stretch px-3 sm:px-4 md:px-8 lg:px-16 xl:px-24 flex flex-col justify-center items-start gap-3">
            <div class="flex flex-col gap-1 sm:gap-2">
                <h1 id="story-detail-heading"
                    class="text-white text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl 2xl:text-7xl font-normal leading-tight">
                    <?= htmlspecialchars($hero->title) ?>
                </h1>
                <?php if ($hero->subtitle !== ''): ?>
                    <p class="text-white text-sm sm:text-base md:text-xl lg:text-2xl xl:text-3xl font-light leading-snug">
                        <?= htmlspecialchars($hero->subtitle) ?>
                    </p>
                <?php endif; ?>
            </div>

            <?php if ($hero->labels !== []): ?>
                <div class="flex flex-wrap gap-2 sm:gap-3 md:gap-5" role="list" aria-label="Event labels">
                    <?php foreach ($hero->labels as $label): ?>
                        <span role="listitem"
                              class="px-4 sm:px-6 py-1.5 sm:py-2.5 bg-red rounded-lg text-white text-base sm:text-xl font-bold">
                            <?= htmlspecialchars($label) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="self-stretch flex flex-col justify-start items-start">
            <div class="pl-2 sm:pl-3 md:pl-4 lg:pl-16 xl:pl-24 pr-2 sm:pr-3 md:pr-4 py-2 sm:py-3 md:py-4
                        bg-sand rounded-tr-[12px] sm:rounded-tr-[15px] md:rounded-tr-[25px] lg:rounded-tr-[35px]
                        flex flex-wrap justify-start items-center gap-2.5 sm:gap-4">
                <a href="<?= htmlspecialchars($hero->backButtonUrl) ?>"
                   class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 md:px-5 py-2 sm:py-2.5 md:py-3.5
                          bg-royal-blue hover:bg-red rounded-lg sm:rounded-xl md:rounded-2xl
                          text-sand text-sm sm:text-base md:text-lg font-normal
                          transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 md:w-5 md:h-5 flex-shrink-0" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                    <?= htmlspecialchars($hero->backButtonLabel) ?>
                </a>
                <a href="<?= htmlspecialchars($hero->reserveButtonUrl) ?>"
                   class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 md:px-6 py-2 sm:py-2.5 md:py-3.5
                          bg-red hover:bg-royal-blue rounded-lg sm:rounded-xl md:rounded-2xl
                          text-white text-sm sm:text-base md:text-lg font-normal
                          transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                    <?= htmlspecialchars($hero->reserveButtonLabel) ?>
                </a>
            </div>
        </div>
    </div>
</section>
