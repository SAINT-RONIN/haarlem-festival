<?php
/**
 * History page view.
 *
 * @var \App\ViewModels\History\HistoryPageViewModel $viewModel
 */

$includeEventSections = true;
$pageContentPartials = [
    __DIR__ . '/../partials/history/route-section.php',
    __DIR__ . '/../partials/history/locations-section.php',
    __DIR__ . '/../partials/history/ticket-options.php',
    __DIR__ . '/../partials/history/info-about-tour.php',
    __DIR__ . '/../partials/history/schedule-section.php',
];
?>
<?php require __DIR__ . '/../partials/_shell.php'; ?>
