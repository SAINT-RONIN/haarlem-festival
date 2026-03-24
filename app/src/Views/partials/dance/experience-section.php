<section class="container mx-auto px-4 py-12">
    <h2 class="text-3xl font-bold mb-8">
        <?= htmlspecialchars($viewModel->experienceData->title) ?>
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($viewModel->experienceData->imageUrls as $imageUrl): ?>
            <img
                src="<?= htmlspecialchars($imageUrl) ?>"
                alt="Dance festival experience"
                class="w-full rounded-xl object-cover"
            >
        <?php endforeach; ?>
    </div>
</section>