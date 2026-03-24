<?php
/**
 * @var array<string, mixed> $viewModel
 */

$artists = $viewModel['artists'];

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

        <!-- HERO -->
        <section class="w-full bg-white py-20">
            <div class="max-w-6xl mx-auto px-6 text-center">
                <h1 class="text-5xl font-extrabold text-slate-900 mb-4">
                    HAARLEM DANCE
                </h1>
                <p class="text-xl text-slate-600">
                    Experience high-energy dance performances at Haarlem's premier music festival.
                </p>
            </div>
        </section>

        <!-- ARTISTS -->
        <section class="w-full py-16 bg-slate-50">
            <div class="max-w-6xl mx-auto px-6">

                <h2 class="text-4xl font-extrabold text-slate-900 mb-10 text-center">
                    Featured Dance Artists
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                    <?php foreach ($artists as $artist): ?>

                        <!-- ✅ THIS IS THE IMPORTANT CLICKABLE CARD -->
                        <a href="/dance/<?= htmlspecialchars($artist['slug']) ?>"
                           class="block bg-white rounded-3xl shadow hover:shadow-xl transition overflow-hidden">

                            <img
                                    src="<?= htmlspecialchars($artist['image']) ?>"
                                    alt="<?= htmlspecialchars($artist['name']) ?>"
                                    class="w-full h-64 object-cover"
                            >

                            <div class="p-6">
                                <h3 class="text-2xl font-bold text-slate-900">
                                    <?= htmlspecialchars($artist['name']) ?>
                                </h3>

                                <p class="text-rose-500 font-semibold mt-2">
                                    <?= htmlspecialchars($artist['genre']) ?>
                                </p>

                                <p class="text-slate-600 mt-3">
                                    <?= htmlspecialchars($artist['description']) ?>
                                </p>
                            </div>

                        </a>

                    <?php endforeach; ?>

                </div>

            </div>
        </section>

    </main>

<?php require __DIR__ . '/../partials/footer.php'; ?>