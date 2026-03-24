<section class="w-full py-20 bg-slate-50 text-slate-900">
    <div class="max-w-6xl mx-auto px-6">
        <h2 class="text-4xl font-bold mb-10">
            <?= htmlspecialchars($experienceData['title'] ?? 'The Festival Experience') ?>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach (($experienceData['images'] ?? []) as $image): ?>
                <img
                        src="<?= htmlspecialchars((string)$image) ?>"
                        alt="Dance experience"
                        class="w-full h-72 object-cover rounded-2xl shadow"
                >
            <?php endforeach; ?>
        </div>
    </div>
</section>