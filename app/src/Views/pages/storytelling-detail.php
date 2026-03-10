<?php
/**
 * Storytelling Detail page view.
 *
 * @var \App\ViewModels\Storytelling\StorytellingDetailPageViewModel $viewModel
 */

$globalUi        = $viewModel->globalUi;
$scheduleSection = $viewModel->scheduleSection;
$currentPage     = 'storytelling';
$includeNav      = false;
$isLoggedIn      = $globalUi->isLoggedIn;
?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="w-full bg-sand inline-flex flex-col justify-start items-center">

    <!-- ============================================================
         1. HERO SECTION – full-bleed with embedded sticky navbar
         ============================================================ -->
    <section class="self-stretch px-1 sm:px-2 pb-1 sm:pb-2 flex flex-col justify-center items-center"
             aria-labelledby="story-detail-heading">
        <div class="self-stretch min-h-[500px] h-[calc(100vh-0.5rem)] sm:h-[calc(100vh-1rem)]
                    rounded-bl-[20px] rounded-br-[20px] sm:rounded-bl-[30px] sm:rounded-br-[30px] md:rounded-bl-[50px] md:rounded-br-[50px]
                    flex flex-col justify-between items-end relative hero-background-base"
             style="background-image: linear-gradient(to bottom, rgba(0,0,0,0.25), rgba(0,0,0,0.65)), url('<?= htmlspecialchars($viewModel->heroImageUrl) ?>');"
             role="img" aria-label="<?= htmlspecialchars($viewModel->title) ?> hero background">

            <!-- Sticky Navigation (floating inside hero) -->
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

                    <!-- Mobile Menu Toggle -->
                    <button type="button" id="detail-menu-btn" data-toggle-menu="detail-nav-menu"
                            class="xl:hidden p-2 sm:p-2.5 mr-1.5 sm:mr-2 text-sand focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2 rounded-lg"
                            aria-expanded="false" aria-controls="detail-nav-menu" aria-label="Toggle navigation menu">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <!-- Nav Links -->
                    <div id="detail-nav-menu"
                         class="hidden xl:flex xl:relative xl:w-auto xl:top-auto xl:right-auto xl:mt-0 xl:rounded-2xl xl:shadow-none
                                absolute top-full right-0 left-0 mt-2 w-full
                                p-2 bg-royal-blue rounded-xl sm:rounded-2xl shadow-lg
                                flex-col xl:flex-row justify-end items-center gap-1.5 xl:gap-2 2xl:gap-3 z-50"
                         role="menubar">
                        <?php
                        $navLinks = [
                            ['href' => '/',             'key' => 'home',         'label' => $globalUi->navHome],
                            ['href' => '/jazz',         'key' => 'jazz',         'label' => $globalUi->navJazz],
                            ['href' => '/dance',        'key' => 'dance',        'label' => $globalUi->navDance],
                            ['href' => '/history',      'key' => 'history',      'label' => $globalUi->navHistory],
                            ['href' => '/restaurant',   'key' => 'restaurant',   'label' => $globalUi->navRestaurant],
                            ['href' => '/storytelling', 'key' => 'storytelling', 'label' => $globalUi->navStorytelling],
                        ];
                        foreach ($navLinks as $link):
                            $isActive = $currentPage === $link['key'];
                        ?>
                            <a href="<?= htmlspecialchars($link['href']) ?>" role="menuitem"
                               class="w-full xl:w-auto px-3 xl:px-3.5 2xl:px-4 py-2 <?= $isActive ? 'bg-red' : 'hover:bg-red' ?> rounded-lg flex justify-center items-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2"
                               <?= $isActive ? 'aria-current="page"' : '' ?>>
                                <span class="text-center text-sand text-sm 2xl:text-base font-normal"><?= htmlspecialchars($link['label']) ?></span>
                            </a>
                        <?php endforeach; ?>

                        <span class="hidden xl:block w-px h-6 bg-sand/30 mx-1 2xl:mx-2" aria-hidden="true"></span>

                        <!-- My Program -->
                        <a href="/program"
                           class="w-full xl:w-auto px-4 xl:px-5 2xl:px-6 py-2 bg-sand hover:bg-red rounded-lg flex justify-center items-center gap-2 transition-colors duration-200 group focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                            <i data-lucide="shopping-cart" class="w-4 h-4 2xl:w-5 2xl:h-5 text-royal-blue group-hover:text-sand transition-colors duration-200" aria-hidden="true"></i>
                            <span class="text-center text-royal-blue group-hover:text-sand text-sm 2xl:text-base font-normal transition-colors duration-200"><?= htmlspecialchars($globalUi->btnMyProgram) ?></span>
                        </a>

                        <!-- Login / Logout -->
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

            <!-- Hero Content: title + labels -->
            <div class="self-stretch px-3 sm:px-4 md:px-8 lg:px-16 xl:px-24 flex flex-col justify-center items-start gap-3">
                <div class="flex flex-col gap-1 sm:gap-2">
                    <h1 id="story-detail-heading"
                        class="text-white text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl 2xl:text-7xl font-normal leading-tight">
                        <?= htmlspecialchars($viewModel->title) ?>
                    </h1>
                    <?php if (!empty($viewModel->subtitle)): ?>
                        <p class="text-white text-sm sm:text-base md:text-xl lg:text-2xl xl:text-3xl font-light leading-snug">
                            <?= htmlspecialchars($viewModel->subtitle) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Labels (In English / For ages 16+) -->
                <?php if (!empty($viewModel->labels)): ?>
                    <div class="flex flex-wrap gap-2 sm:gap-3 md:gap-5" role="list" aria-label="Event labels">
                        <?php foreach ($viewModel->labels as $label): ?>
                            <span role="listitem"
                                  class="px-4 sm:px-6 py-1.5 sm:py-2.5 bg-red rounded-lg text-white text-base sm:text-xl font-bold">
                                <?= htmlspecialchars($label) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Hero Footer: Back + Reserve buttons -->
            <div class="self-stretch flex flex-col justify-start items-start">
                <div class="px-3 sm:px-4 md:px-8 lg:px-16 xl:px-24 py-2 sm:py-3 md:py-4 lg:py-5
                            bg-sand rounded-tr-[12px] sm:rounded-tr-[25px] md:rounded-tr-[35px]
                            inline-flex flex-wrap justify-start items-center gap-2 sm:gap-3 md:gap-5">
                    <a href="/storytelling"
                       class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 md:px-5 py-2 sm:py-2.5 md:py-3.5
                              bg-royal-blue hover:bg-red rounded-lg sm:rounded-xl md:rounded-2xl
                              text-sand text-sm sm:text-base md:text-lg font-normal
                              transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 md:w-5 md:h-5 flex-shrink-0" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2.5"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M15 18l-6-6 6-6"/>
                        </svg>
                        <?= htmlspecialchars($viewModel->backButtonLabel) ?>
                    </a>
                    <a href="#storytelling-detail-schedule"
                       class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 md:px-6 py-2 sm:py-2.5 md:py-3.5
                              bg-red hover:bg-royal-blue rounded-lg sm:rounded-xl md:rounded-2xl
                              text-white text-sm sm:text-base md:text-lg font-normal
                              transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                        <?= htmlspecialchars($viewModel->reserveButtonLabel) ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
         2. ABOUT SECTION – two images left, text right
         ============================================================ -->
    <section class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-10 md:py-12
                    flex flex-col lg:flex-row justify-center items-center gap-8 md:gap-10 lg:gap-12"
             aria-labelledby="about-heading">
        <!-- Images -->
        <div class="w-full lg:flex-1 flex flex-col sm:flex-row gap-4 md:gap-6 lg:gap-8">
            <img src="<?= htmlspecialchars($viewModel->aboutImage1Url) ?>"
                 alt="<?= htmlspecialchars($viewModel->aboutHeading) ?>"
                 class="flex-1 w-full sm:w-1/2 aspect-square object-cover rounded-2xl">
            <img src="<?= htmlspecialchars($viewModel->aboutImage2Url) ?>"
                 alt="<?= htmlspecialchars($viewModel->aboutHeading) ?>"
                 class="flex-1 w-full sm:w-1/2 aspect-square object-cover rounded-2xl">
        </div>
        <!-- Text -->
        <div class="w-full lg:flex-1 flex flex-col gap-3 sm:gap-4 md:gap-5">
            <h2 id="about-heading"
                class="text-royal-blue text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold">
                <?= htmlspecialchars($viewModel->aboutHeading) ?>
            </h2>
            <div class="text-royal-blue text-base sm:text-lg md:text-xl font-normal leading-7 sm:leading-8">
                <?= $viewModel->aboutBodyHtml ?>
            </div>
        </div>
    </section>

    <!-- ============================================================
         3. STORY HIGHLIGHTS – 3 cards
         ============================================================ -->
    <?php if (!empty($viewModel->highlights)): ?>
    <section class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-10 md:py-12
                    flex flex-col gap-5 sm:gap-6 md:gap-8"
             aria-labelledby="highlights-heading">
        <h2 id="highlights-heading"
            class="text-royal-blue text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold">
            <?= htmlspecialchars($viewModel->highlightsSectionHeading) ?>
        </h2>
        <div class="w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 lg:gap-12">
            <?php foreach ($viewModel->highlights as $highlight): ?>
                <article class="bg-white rounded-2xl shadow-[0px_0px_24px_-2px_rgba(0,0,0,0.25)] flex flex-col overflow-hidden">
                    <img src="<?= htmlspecialchars($highlight->imageUrl) ?>"
                         alt="<?= htmlspecialchars($highlight->title) ?>"
                         class="w-full h-56 sm:h-64 md:h-72 lg:h-96 object-cover">
                    <div class="flex-1 p-3.5 flex flex-col gap-3 sm:gap-5">
                        <h3 class="text-royal-blue text-lg sm:text-xl md:text-2xl font-semibold">
                            <?= htmlspecialchars($highlight->title) ?>
                        </h3>
                        <p class="text-royal-blue text-base sm:text-lg md:text-xl font-normal leading-7">
                            <?= htmlspecialchars($highlight->description) ?>
                        </p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ============================================================
         4. WHERE STORIES COME ALIVE – gallery grid
         ============================================================ -->
    <section class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-10 md:py-12
                    flex flex-col gap-5 sm:gap-6 md:gap-8"
             aria-labelledby="gallery-heading">
        <h2 id="gallery-heading"
            class="text-royal-blue text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold">
            <?= htmlspecialchars($viewModel->gallerySectionHeading) ?>
        </h2>
        <!-- Top row: 3 images -->
        <?php if (!empty($viewModel->galleryImages)): ?>
        <div class="w-full flex flex-col gap-4 md:gap-6 lg:gap-12">
            <div class="w-full grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6 lg:gap-12 h-auto sm:h-72 md:h-80 lg:h-96">
                <?php foreach (array_slice($viewModel->galleryImages, 0, 3) as $imgUrl): ?>
                    <div class="rounded-3xl overflow-hidden h-48 sm:h-full">
                        <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($viewModel->gallerySectionHeading) ?>"
                             class="w-full h-full object-cover">
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Bottom row: 2 images -->
            <?php $bottomImages = array_slice($viewModel->galleryImages, 3, 2); ?>
            <?php if (!empty($bottomImages)): ?>
            <div class="w-full grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6 lg:gap-12 h-auto sm:h-[300px] md:h-[400px] lg:h-[500px]">
                <?php foreach ($bottomImages as $imgUrl): ?>
                    <div class="rounded-3xl overflow-hidden h-48 sm:h-full">
                        <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($viewModel->gallerySectionHeading) ?>"
                             class="w-full h-full object-cover">
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </section>

    <!-- ============================================================
         5. A MOMENT FROM THE SHOW – video embed
         ============================================================ -->
    <section class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-10 md:py-12
                    flex flex-col gap-5 sm:gap-6 md:gap-8"
             aria-labelledby="video-heading">
        <h2 id="video-heading"
            class="text-royal-blue text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold">
            <?= htmlspecialchars($viewModel->videoSectionHeading) ?>
        </h2>
        <div class="w-full rounded-2xl overflow-hidden bg-royal-blue/10 aspect-video flex items-center justify-center">
            <?php if (!empty($viewModel->videoUrl)): ?>
                <iframe
                    src="<?= htmlspecialchars($viewModel->videoUrl) ?>"
                    class="w-full h-full"
                    title="<?= htmlspecialchars($viewModel->videoSectionHeading) ?>"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    loading="lazy">
                </iframe>
            <?php else: ?>
                <div class="w-full h-full min-h-[300px] sm:min-h-[400px] lg:min-h-[600px]
                            bg-royal-blue/10 rounded-2xl flex flex-col items-center justify-center gap-4 p-8">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-royal-blue/20 flex items-center justify-center">
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 text-royal-blue" viewBox="0 0 24 24"
                             fill="currentColor" aria-hidden="true">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
                    <p class="text-royal-blue/60 text-lg font-normal text-center"><?= htmlspecialchars($viewModel->videoPlaceholderText) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ============================================================
         6. STORYTELLING SCHEDULE
         ============================================================ -->
    <?php require __DIR__ . '/../partials/sections/schedule/schedule-section.php'; ?>

</main>

<script src="/assets/js/menu-toggle.js"></script>
<?php require __DIR__ . '/../partials/footer.php'; ?>
