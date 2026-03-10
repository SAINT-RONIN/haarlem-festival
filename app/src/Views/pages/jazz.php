<?php
/**
 * Jazz page view.
 *
 * @var \App\ViewModels\Jazz\JazzPageViewModel $viewModel
 */

$pageContentPartials = [
    __DIR__ . '/../partials/jazz/venues-section.php',
    __DIR__ . '/../partials/jazz/pricing-section.php',
    __DIR__ . '/../partials/jazz/schedule-cta-section.php',
    __DIR__ . '/../partials/jazz/artists-section.php',
    __DIR__ . '/../partials/jazz/booking-cta-section.php',
];
$includeEventSections = true;

if ($viewModel->scheduleSection !== null) {
    $pageContentPartials[] = __DIR__ . '/../partials/sections/schedule/schedule-section.php';
}
?>
<?php require __DIR__ . '/../partials/_shell.php'; ?>
