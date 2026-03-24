<?php
/**
 * @var array<string, mixed> $viewModel
 */

$heroData = $viewModel['heroData'] ?? [
        'mainTitle' => 'HAARLEM DANCE',
        'subtitle' => "Experience high-energy dance performances at Haarlem's premier music festival. Discover our complete lineup, detailed schedules, and venue information.",
        'primaryButtonText' => 'Discover all performances',
        'primaryButtonLink' => '/dance#artists',
        'secondaryButtonText' => 'What is Haarlem Dance?',
        'secondaryButtonLink' => '/dance#about',
        'backgroundImageUrl' => '/assets/Image/Image (Dance).png',
];

$globalUi = $viewModel['globalUi'] ?? [
        'siteName' => 'Haarlem Festival',
        'navHome' => 'Home',
        'navJazz' => 'Jazz',
        'navDance' => 'Dance',
        'navHistory' => 'History',
        'navRestaurant' => 'Restaurant',
        'navStorytelling' => 'Storytelling',
        'btnMyProgram' => 'My Program',
        'isLoggedIn' => false,
];

$gradientSection = $viewModel['gradientSection'] ?? [
        'headingText' => 'Every beat carries energy, movement, and connection beyond what is heard.',
        'subheadingText' => 'A place where dance is experienced, not just played.',
        'backgroundImageUrl' => '/assets/Image/Image (Dance).png',
];

$introSplitSection = $viewModel['introSplitSection'] ?? [
        'headingText' => 'Move to the rhythm of Haarlem Dance',
        'bodyText' => "Haarlem Dance brings together electronic music, unforgettable artists, and vibrant performances across the city.\n\nExplore featured artists, detailed schedules, venues, and ticket options.",
        'imageUrl' => '/assets/Image/Image (Dance).png',
        'imageAltText' => 'Dance festival performance',
];

$artists = $viewModel['artists'] ?? [];

$currentPage = 'dance';
$includeNav = false;

$cms = [
        'hero_section' => [
                'hero_main_title' => $heroData['mainTitle'],
                'hero_subtitle' => $heroData['subtitle'],
                'hero_button_primary' => $heroData['primaryButtonText'],
                'hero_button_primary_link' => $heroData['primaryButtonLink'],
                'hero_button_secondary' => $heroData['secondaryButtonText'],
                'hero_button_secondary_link' => $heroData['secondaryButtonLink'],
                'hero_background_image' => $heroData['backgroundImageUrl'],
        ],
        'global_ui' => [
                'site_name' => $globalUi['siteName'],
                'nav_home' => $globalUi['navHome'],
                'nav_jazz' => $globalUi['navJazz'],
                'nav_dance' => $globalUi['navDance'],
                'nav_history' => $globalUi['navHistory'],
                'nav_restaurant' => $globalUi['navRestaurant'],
                'nav_storytelling' => $globalUi['navStorytelling'],
                'btn_my_program' => $globalUi['btnMyProgram'],
                'is_logged_in' => $globalUi['isLoggedIn'],
        ],
];
?>

<?php require __DIR__ . '/../partials/header.php'; ?>

    <main class="w-full bg-sand inline-flex flex-col justify-start items-center">

        <?php require __DIR__ . '/../partials/hero.php'; ?>

        <section class="w-full bg-white py-16 md:py-24">
            <div class="max-w-6xl mx-auto px-6">
                <div class="relative rounded-[32px] overflow-hidden min-h-[360px] md:min-h-[420px]">
                    <img
                            src="<?= htmlspecialchars($gradientSection['backgroundImageUrl']) ?>"
                            alt="Dance atmosphere"
                            class="absolute inset-0 w-full h-full object-cover"
                    >
                    <div class="absolute inset-0 bg-black/50"></div>

                    <div class="relative z-10 h-full flex flex-col justify-center px-8 md:px-16 py-12 text-white">
                        <h2 class="text-4xl md:text-6xl font-extrabold leading-tight max-w-4xl mb-6">
                            <?= htmlspecialchars($gradientSection['headingText']) ?>
                        </h2>

                        <p class="text-xl md:text-3xl leading-snug max-w-3xl">
                            <?= htmlspecialchars($gradientSection['subheadingText']) ?>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section id="about" class="w-full bg-white py-16 md:py-24">
            <div class="max-w-6xl mx-auto px-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-16 items-center">
                    <div>
                        <h2 class="text-[42px] md:text-[64px] font-extrabold leading-[0.95] text-slate-900 mb-8">
                            <?= nl2br(htmlspecialchars($introSplitSection['headingText'])) ?>
                        </h2>

                        <div class="text-lg md:text-2xl leading-9 text-slate-700 space-y-6">
                            <?php foreach (preg_split("/\n\s*\n/", trim((string) $introSplitSection['bodyText'])) as $paragraph): ?>
                                <?php if (trim($paragraph) === '') continue; ?>
                                <p><?= htmlspecialchars($paragraph) ?></p>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div>
                        <img
                                src="<?= htmlspecialchars($introSplitSection['imageUrl']) ?>"
                                alt="<?= htmlspecialchars($introSplitSection['imageAltText']) ?>"
                                class="w-full h-full max-h-[760px] object-cover rounded-[28px]"
                        >
                    </div>
                </div>
            </div>
        </section>

        <section id="artists" class="w-full py-20 bg-slate-50 text-slate-900">
            <div class="max-w-6xl mx-auto px-6">
                <h2 class="text-4xl md:text-5xl font-bold mb-10">Featured Dance Artists</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($artists as $artist): ?>
                        <a href="/dance/<?= htmlspecialchars($artist['slug']) ?>" class="block bg-white rounded-3xl shadow hover:shadow-lg transition overflow-hidden">
                            <img
                                    src="<?= htmlspecialchars($artist['image'] ?? $artist['heroImage'] ?? '/assets/Image/Image (Dance).png') ?>"
                                    alt="<?= htmlspecialchars($artist['name']) ?>"
                                    class="w-full h-72 object-cover"
                            >

                            <div class="p-6">
                                <h3 class="text-3xl font-bold mb-2"><?= htmlspecialchars($artist['name']) ?></h3>
                                <p class="text-rose-500 font-semibold mb-4"><?= htmlspecialchars($artist['genre']) ?></p>
                                <p class="text-slate-700 text-lg leading-7"><?= htmlspecialchars($artist['description']) ?></p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

    </main>

<?php require __DIR__ . '/../partials/footer.php'; ?>