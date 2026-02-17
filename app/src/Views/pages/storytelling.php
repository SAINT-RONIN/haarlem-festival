<?php
/**
 * Storytelling page view.
 *
 * @var \App\ViewModels\Storytelling\StorytellingPageViewModel $viewModel
 */
?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="w-full bg-sand inline-flex flex-col justify-start items-center">

    <?php require __DIR__ . '/../partials/hero.php'; ?>

    <?php require __DIR__ . '/../partials/sections/gradient-section.php'; ?>

    <?php require __DIR__ . '/../partials/sections/intro-split-section.php'; ?>

    <?php require __DIR__ . '/../partials/storytelling-masonry-section.php'; ?>

    <?php require __DIR__ . '/../partials/sections/schedule/schedule-section.php'; ?>

</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
