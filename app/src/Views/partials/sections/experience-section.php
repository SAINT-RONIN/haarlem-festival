<?php
/**
 * Experience section partial.
 *
 * @var \App\ViewModels\Dance\ExperienceData $experienceData
 */
?>

<section class="w-full max-w-[1200px] px-6 py-14">
    <h2 class="text-center text-2xl font-extrabold tracking-wide mb-6">
        <?= htmlspecialchars($experienceData->title) ?>
    </h2>

    <div class="w-full overflow-x-auto flex gap-4 snap-x snap-mandatory pb-2">
        <?php foreach ($experienceData->imageUrls as $img): ?>
            <img
                    class="w-[320px] h-[220px] object-cover rounded-xl snap-start"
                    src="<?= htmlspecialchars($img) ?>"
                    alt="Festival experience"
                    loading="lazy"
            />
        <?php endforeach; ?>
    </div>
</section>
