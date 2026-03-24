<?php
/**
 * @var \App\ViewModels\Dance\DancePageViewModel $viewModel
 */

$pageContentPartials = [
    __DIR__ . '/../partials/dance/intro-highlight-section.php',
    __DIR__ . '/../partials/dance/experience-section.php',
];

if ($viewModel->scheduleSection !== null) {
    $pageContentPartials[] = __DIR__ . '/../partials/sections/schedule/schedule-section.php';
}
?>
<?php require __DIR__ . '/../partials/_shell.php'; ?>