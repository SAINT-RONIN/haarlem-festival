<?php
/**
 * Renders the masonry image grid section on the Storytelling overview page.
 * The reason for this is because the masonry layout is a visually distinct,
 * self-contained section that would make the overview page template harder to
 * read if its column and image logic were inlined there.
 *
 * @var \App\ViewModels\Storytelling\StorytellingPageViewModel $viewModel
 */

$masonrySection = $viewModel->masonrySection;
?>

<!-- Masonry Grid Section -->
<section class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-10 md:py-12 flex flex-col gap-6">
    <!-- Section Heading -->
    <h2 class="text-slate-800 text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold font-['Montserrat']">
        <?= htmlspecialchars($masonrySection->headingText) ?>
    </h2>

    <!-- Masonry Grid: CSS multi-column layout -->
    <div class="columns-1 md:columns-2 xl:columns-4 gap-4 [column-fill:balance]">
        <?php foreach ($masonrySection->images as $image): ?>
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
