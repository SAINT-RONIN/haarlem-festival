<?php
/**
 * Masonry section partial for storytelling page.
 * True masonry layout using CSS multi-column.
 * Responsive: 4 columns desktop, 2 columns tablet, 1 column mobile.
 *
 * Required variable (set before including this partial):
 * @var \App\ViewModels\MasonrySectionData $masonrySection
 */
?>

<!-- Masonry Grid Section -->
<section class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-10 md:py-12 flex flex-col gap-6">
    <!-- Section Heading -->
    <h2 class="text-slate-800 text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold font-['Montserrat']">
        <?= htmlspecialchars($masonrySection->headingText) ?>
    </h2>

    <!-- Masonry Grid: CSS multi-column layout -->
    <div class="columns-1 md:columns-2 xl:columns-4 gap-4">
        <?php foreach ($masonrySection->columns as $columnImages): ?>
            <?php foreach ($columnImages as $image): ?>
                <div class="break-inside-avoid mb-4">
                    <img
                            class="w-full object-cover rounded-2xl <?= htmlspecialchars($image->sizeClass) ?>"
                            src="<?= htmlspecialchars($image->imageUrl) ?>"
                            alt="<?= htmlspecialchars($image->altText) ?>"
                            loading="lazy">
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
</section>

