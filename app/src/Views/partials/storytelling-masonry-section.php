<?php
/**
 * Masonry section partial for storytelling page.
 *
 * True masonry-style layout using CSS multi-column.
 * Responsive: 1 column mobile, 2 columns tablet, 4 columns desktop.
 *
 * Required variable (set before including this partial):
 * @var \App\ViewModels\Storytelling\MasonrySectionData $masonrySection
 */

if (!isset($masonrySection) && isset($viewModel) && property_exists($viewModel, 'masonrySection')) {
    $masonrySection = $viewModel->masonrySection;
}

if (!isset($masonrySection)) {
    return;
}
?>

<!-- Masonry Grid Section -->
<section class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-10 md:py-12 flex flex-col gap-6">
    <!-- Section Heading -->
    <h2 class="text-slate-800 text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold font-['Montserrat']">
        <?= htmlspecialchars($masonrySection->headingText) ?>
    </h2>

    <!-- Masonry Grid: CSS multi-column layout -->
    <div class="columns-1 md:columns-2 xl:columns-4 gap-4 [column-fill:balance]">
        <?php
        // Flatten columns into a single list so the browser can naturally
        // flow items into CSS multi-columns without our PHP grouping
        $allImages = [];
foreach ($masonrySection->columns as $columnImages) {
    foreach ($columnImages as $image) {
        $allImages[] = $image;
    }
}
?>

        <?php foreach ($allImages as $image): ?>
            <figure class="break-inside-avoid mb-4 overflow-hidden rounded-2xl">
                <img
                    class="w-full h-auto object-cover align-top <?= htmlspecialchars($image->sizeClass) ?>"
                    src="<?= htmlspecialchars($image->imageUrl) ?>"
                    alt="<?= htmlspecialchars($image->altText) ?>"
                    loading="lazy">
            </figure>
        <?php endforeach; ?>
    </div>
</section>
