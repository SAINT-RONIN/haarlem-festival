<section class="container mx-auto px-4 py-12">
    <div class="grid md:grid-cols-2 gap-10 items-center">
        <div>
            <h2 class="text-3xl font-bold mb-4">
                <?= htmlspecialchars($viewModel->introSplitSection->headingText) ?>
            </h2>
            <p class="text-base leading-7">
                <?= nl2br(htmlspecialchars($viewModel->introSplitSection->bodyText)) ?>
            </p>
        </div>
        <div>
            <img
                src="<?= htmlspecialchars($viewModel->introSplitSection->imageUrl) ?>"
                alt="<?= htmlspecialchars($viewModel->introSplitSection->imageAltText) ?>"
                class="w-full rounded-xl"
            >
        </div>
    </div>
</section>