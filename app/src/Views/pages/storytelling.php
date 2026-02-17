<?php
/**
 * Storytelling page view.
 *
 * @var \App\ViewModels\Storytelling\StorytellingPageViewModel $viewModel
 */

$includeEventSections = true;
$pageContentPartials = [
    __DIR__ . '/../partials/storytelling-masonry-section.php',
    __DIR__ . '/../partials/sections/schedule/schedule-section.php',
];
?>
<?php require __DIR__ . '/../partials/_shell.php'; ?>
