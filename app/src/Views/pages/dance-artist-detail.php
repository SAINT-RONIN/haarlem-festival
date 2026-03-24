<?php
/**
 * @var array<string, mixed> $viewModel
 */

$heroData = $viewModel['heroData'] ?? [];
$globalUi = $viewModel['globalUi'] ?? [];
$gradientSection = $viewModel['gradientSection'] ?? [];
$introSplitSection = $viewModel['introSplitSection'] ?? [];
$artists = $viewModel['artists'] ?? [];

$currentPage = 'dance';
$includeNav = false;

$cms = [
    'hero_section' => [
        'hero_main_title' => $heroData['mainTitle'] ?? 'HAARLEM DANCE',
        'hero_subtitle' => $heroData['subtitle'] ?? '',
        'hero_button_primary' => $heroData['primaryButtonText'] ?? 'Discover all performances',
        'hero_button_primary_link' => $heroData['primaryButtonLink'] ?? '/dance#artists',
        'hero_button_secondary' => $heroData['secondaryButtonText'] ?? 'What is Haarlem Dance?',
        'hero_button_secondary_link' => $heroData['secondaryButtonLink'] ?? '/dance#about',
        'hero_background_image' => $heroData['backgroundImageUrl'] ?? '/assets/Image/Image (Dance).png',
    ],
    'global_ui' => [
        'site_name' => $globalUi['siteName'] ?? 'Haarlem Festival',
        'nav_home' => $globalUi['navHome'] ?? 'Home',
        'nav_jazz' => $globalUi['navJazz'] ?? 'Jazz',
        'nav_dance' => $globalUi['navDance'] ?? 'Dance',
        'nav_history' => $globalUi['navHistory'] ?? 'History',
        'nav_restaurant' => $globalUi['navRestaurant'] ?? 'Restaurant',
        'nav_storytelling' => $globalUi['navStorytelling'] ?? 'Storytelling',
        'btn_my_program' => $globalUi['btnMyProgram'] ?? 'My Program',
        'is_logged_in' => $globalUi['isLoggedIn'] ?? false,
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
                        src="<?= htmlspecialchars($gradientSection['backgroundImageUrl'] ?? '/assets/Image/dance/banner.jpg') ?>"
                        alt="Dance atmosphere"
                        class="absolute inset-0 w-full h-full object-cover"
                    >
                    <div class="absolute inset-0 bg-black/50"></div>

                    <div class="relative z-10 h-full flex flex-col justify-center px-8 md:px-16 py-12 text-white">
                        <h2 class="text-4xl md:text-6xl font-extrabold leading-tight max-w-4xl mb-6">
                            <?= htmlspecialchars($gradientSection['headingText'] ?? 'Every beat carries energy, movement, and connection beyond what is heard.') ?>
                        </h2>

                        <p class="text-xl md:text-3xl leading-snug max-w-3xl">
                            <?= htmlspecialchars($gradientSection['subheadingText'] ?? 'A place where dance is experienced, not just played.') ?>
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
                            <?= nl2br(htmlspecialchars($introSplitSection['headingText'] ?? 'Move to the rhythm of Haarlem Dance')) ?>
                        </h2>

                        <div class="text-lg md:text-2xl leading-9 text-slate-700 space-y-6">
                            <?php
                            $bodyText = $introSplitSection['bodyText'] ?? '';
                            foreach (preg_split("/\n\s*\n/", trim((string) $bodyText)) as $paragraph):
                                if ($paragraph === '') {
                                    continue;
                                }
                                ?>
                                <p><?= htmlspecialchars($paragraph) ?></p>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div>
                        <img
                            src="<?= htmlspecialchars($introSplitSection['imageUrl'] ?? '/assets/Image/dance/dance.jpg') ?>"
                            alt="<?= htmlspecialchars($introSplitSection['imageAltText'] ?? 'Dance festival performance') ?>"
                            class="w-full h-full max-h-[760px] object-cover rounded-[28px]"
                        >
                    </div>
                </div>
            </div>
        </section>

        <section id="artists" class="w-full py-20 bg-slate-50 text-slate-900">
            <div class="max-w-6xl mx-auto px-6">
                <h2 class="text-4xl md:text-5xl font-bold mb-10">Featured Dance Artists</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <?php foreach ($artists as $artist): ?>
                        <a href="/dance/<?= htmlspecialchars($artist['slug']) ?>" class="block bg-white rounded-3xl shadow hover:shadow-lg transition overflow-hidden">
                            <img
                                src="<?= htmlspecialchars($artist['image']) ?>"
                                alt="<?= htmlspecialchars($artist['name']) ?>"
                                class="w-full h-80 object-cover"
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