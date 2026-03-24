<?php
/**
 * @var array<string, mixed> $viewModel
 */

$artist = $viewModel;

$currentPage = 'dance';
$includeNav = false;

$cms = [
        'global_ui' => [
                'site_name' => 'Haarlem Festival',
                'nav_home' => 'Home',
                'nav_jazz' => 'Jazz',
                'nav_dance' => 'Dance',
                'nav_history' => 'History',
                'nav_restaurant' => 'Restaurant',
                'nav_storytelling' => 'Storytelling',
                'btn_my_program' => 'My Program',
                'is_logged_in' => false,
        ],
];
?>
<?php require __DIR__ . '/../partials/header.php'; ?>

    <main class="w-full bg-sand inline-flex flex-col justify-start items-center">

        <section class="w-full bg-white py-12 md:py-20">
            <div class="max-w-6xl mx-auto px-6">
                <a href="/dance" class="text-rose-500 font-semibold mb-6 inline-block">← Back to home page</a>

                <h1 class="text-5xl md:text-6xl font-extrabold text-slate-900 mb-2">
                    <?= htmlspecialchars($artist['name']) ?>
                </h1>

                <p class="text-lg md:text-xl text-slate-600 font-semibold mb-8">
                    <?= htmlspecialchars($artist['genre']) ?>
                </p>

                <div class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] gap-6 items-start">
                    <div>
                        <img
                                src="<?= htmlspecialchars($artist['heroImage']) ?>"
                                alt="<?= htmlspecialchars($artist['name']) ?>"
                                class="w-full h-[360px] md:h-[430px] object-cover rounded-2xl"
                        >

                        <div class="grid grid-cols-3 gap-4 mt-4">
                            <?php foreach ($artist['gallery'] as $image): ?>
                                <img
                                        src="<?= htmlspecialchars($image) ?>"
                                        alt="<?= htmlspecialchars($artist['name']) ?>"
                                        class="w-full h-24 md:h-28 object-cover rounded-xl"
                                >
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="grid gap-4">
                        <?php foreach ($artist['videos'] as $videoLabel): ?>
                            <div class="bg-white border rounded-xl shadow-sm p-3">
                                <div class="w-full h-40 bg-slate-200 rounded-lg flex items-center justify-center text-slate-600 text-sm">
                                    <?= htmlspecialchars($videoLabel) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="w-full bg-slate-50 py-10">
            <div class="max-w-6xl mx-auto px-6">
                <h2 class="text-4xl font-extrabold text-slate-900 mb-6">CAREER HIGHLIGHTS</h2>

                <div class="bg-white rounded-2xl shadow p-8">
                    <ul class="list-disc pl-6 space-y-4 text-lg text-slate-700">
                        <?php foreach ($artist['highlights'] as $highlight): ?>
                            <li><?= htmlspecialchars($highlight) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </section>

        <section class="w-full bg-white py-10">
            <div class="max-w-6xl mx-auto px-6">
                <h2 class="text-4xl font-extrabold text-slate-900 mb-8">ESSENTIAL ALBUMS & EPS</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <?php foreach ($artist['albums'] as $album): ?>
                        <div class="bg-slate-50 rounded-2xl shadow p-4">
                            <img
                                    src="<?= htmlspecialchars($album['image']) ?>"
                                    alt="<?= htmlspecialchars($album['title']) ?>"
                                    class="w-full h-80 object-cover rounded-xl mb-4"
                            >
                            <h3 class="text-2xl font-bold text-slate-900">
                                <?= htmlspecialchars($album['title']) ?>
                            </h3>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="w-full bg-slate-50 py-10">
            <div class="max-w-6xl mx-auto px-6">
                <h2 class="text-4xl font-extrabold text-slate-900 mb-8">FESTIVAL SCHEDULE & PRICES</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <?php foreach ($artist['schedule'] as $slot): ?>
                        <div class="bg-white rounded-2xl shadow p-6">
                            <p class="text-lg font-bold text-slate-900"><?= htmlspecialchars($slot['day']) ?></p>
                            <p class="text-slate-700 mt-2"><?= htmlspecialchars($slot['time']) ?></p>
                            <p class="text-slate-700"><?= htmlspecialchars($slot['venue']) ?></p>
                            <p class="text-rose-500 font-semibold mt-4"><?= htmlspecialchars($slot['price']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

    </main>

<?php require __DIR__ . '/../partials/footer.php'; ?>