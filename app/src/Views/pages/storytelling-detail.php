<?php
/**
 * Storytelling detail page view.
 *
 * @var \App\ViewModels\Storytelling\StorytellingDetailPageViewModel $viewModel
 */

$includeHero = false;
$pageContentPartials = [
    __DIR__ . '/../partials/storytelling/detail-hero.php',
    __DIR__ . '/../partials/storytelling/detail-content.php',
    __DIR__ . '/../partials/sections/schedule/schedule-section.php',
];
?>
<?php require __DIR__ . '/../partials/_shell.php'; ?>
