<?php
/**
 * Storytelling page view.
 *
 * @var \App\ViewModels\StorytellingPageViewModel $viewModel
 */

$heroData = $viewModel->heroData;
$globalUi = $viewModel->globalUi;
$gradientSection = $viewModel->gradientSection;
$introSplitSection = $viewModel->introSplitSection;
$masonrySection = $viewModel->masonrySection;
$currentPage = 'storytelling';
$includeNav = false;
?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="w-full bg-sand inline-flex flex-col justify-start items-center">

    <?php require __DIR__ . '/../partials/hero.php'; ?>

    <?php require __DIR__ . '/../partials/storytelling/gradient-section.php'; ?>

    <?php require __DIR__ . '/../partials/storytelling/intro-split-section.php'; ?>

    <?php require __DIR__ . '/../partials/storytelling/masonry-section.php'; ?>

</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>

