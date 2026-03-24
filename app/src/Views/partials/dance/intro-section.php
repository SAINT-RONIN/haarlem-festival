<section class="py-20 bg-white text-slate-900">
    <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-2 gap-10 items-center">
        <div>
            <h2 class="text-4xl font-bold mb-6">
                <?= htmlspecialchars($danceData['intro']['heading']) ?>
            </h2>
            <p class="text-lg leading-8">
                <?= htmlspecialchars($danceData['intro']['body']) ?>
            </p>
        </div>

        <div>
            <img
                src="<?= htmlspecialchars($danceData['intro']['image']) ?>"
                alt="<?= htmlspecialchars($danceData['intro']['imageAlt']) ?>"
                class="w-full rounded-2xl shadow-lg object-cover"
            >
        </div>
    </div>
</section>