<?php
/**
 * Header partial - HTML head only (navigation is now in hero for homepage, or standalone for other pages).
 *
 * @var string $currentPage Current page identifier for nav highlighting
 * @var bool $includeNav Whether to include standalone navigation (false for homepage where nav is in hero)
 * @var bool $isLoggedIn Whether user is logged in (passed from controller/ViewModel, defaults to false)
 */

use App\View\ViewRenderer;

$currentPage = $currentPage ?? 'home';
$includeNav = $includeNav ?? false;
$useLayoutWrapper = $useLayoutWrapper ?? false;
$isLoggedIn = $isLoggedIn ?? false;
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haarlem Festival | Four Days, Countless Experiences</title>
    <meta name="description" content="Four Days, Countless Experiences">
    <link rel="icon" href="/assets/Icons/Logo.svg" type="image/svg+xml" sizes="any">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@0.460.0"></script>
    <script src="/assets/js/lucide-init.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">
    <!-- tokens.css must be loaded BEFORE components.css. It defines the global color variables and utility classes that components.css relies on. Loading it first ensures consistent colors and avoids override or compatibility issues. -->
    <link rel="stylesheet" href="/assets/css/tokens.css">
    <link rel="stylesheet" href="/assets/css/components.css">
</head>
<body class="bg-sand">

<?php if ($useLayoutWrapper): ?>
<div class="min-h-screen flex flex-col">
    <?php endif; ?>

    <?php if ($includeNav): ?>
        <?php ViewRenderer::render(__DIR__ . '/navbar.php', [
            'currentPage' => $currentPage,
            'isLoggedIn' => $isLoggedIn,
        ]); ?>
    <?php endif; ?>
