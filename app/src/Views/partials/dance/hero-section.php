<section class="relative min-h-[70vh] flex items-center text-white overflow-hidden">
    <img
        src="<?= htmlspecialchars($danceData['hero']['backgroundImage']) ?>"
        alt="Dance hero background"
        class="absolute inset-0 w-full h-full object-cover"
    >
    <div class="absolute inset-0 bg-black/50"></div>

    <div class="relative z-10 max-w-6xl mx-auto px-6 py-24 w-full">
        <h1 class="text-5xl md:text-7xl font-bold uppercase mb-4">
            <?= htmlspecialchars($danceData['hero']['title']) ?>
        </h1>

        <p class="text-xl md:text-2xl max-w-4xl mb-8">
            <?= htmlspecialchars($danceData['hero']['subtitle']) ?>
        </p>

        <div class="flex flex-wrap gap-4">
            <a
                href="<?= htmlspecialchars($danceData['hero']['primaryButtonLink']) ?>"
                class="inline-flex items-center rounded-full bg-rose-500 px-6 py-3 text-white font-semibold hover:bg-rose-600 transition"
            >
                <?= htmlspecialchars($danceData['hero']['primaryButtonText']) ?>
            </a>

            <a
                href="<?= htmlspecialchars($danceData['hero']['secondaryButtonLink']) ?>"
                class="inline-flex items-center rounded-full border border-white px-6 py-3 text-white font-semibold hover:bg-white hover:text-slate-900 transition"
            >
                <?= htmlspecialchars($danceData['hero']['secondaryButtonText']) ?>
            </a>
        </div>
    </div>
</section>