<?php
/**
 * Renders the main Storytelling overview page.
 * The reason for this is because it acts as the entry point for the Storytelling
 * event type, composing the shell, masonry image grid, and schedule into one page.
 *
 * @var \App\ViewModels\Storytelling\StorytellingPageViewModel $viewModel
 */

$includeEventSections = true;
$pageContentPartials = [
    // Displays the masonry image grid that introduces the storytelling event visually.
    __DIR__ . '/../partials/storytelling-masonry-section.php',
    // Displays the full session schedule so visitors can browse available dates and times.
    __DIR__ . '/../partials/sections/schedule/schedule-section.php',
];
?>
<?php require __DIR__ . '/../partials/_shell.php'; ?>
