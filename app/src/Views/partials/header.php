<?php
/**
 * Header partial - HTML head only (navigation is now in hero for homepage, or standalone for other pages).
 *
 * @var string $currentPage Current page identifier for nav highlighting
 * @var bool $includeNav Whether to include standalone navigation (false for homepage where nav is in hero)
 */
$currentPage = $currentPage ?? 'home';
$includeNav = $includeNav ?? false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haarlem Festival</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; }
        .font-serif-display { font-family: 'Georgia', serif; }
        .hf-container {
            max-width: 1440px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 1rem;
            padding-right: 1rem;
        }
        @media (min-width: 768px) {
            .hf-container { padding-left: 3rem; padding-right: 3rem; }
        }
        @media (min-width: 1024px) {
            .hf-container { padding-left: 4rem; padding-right: 4rem; }
        }
        @media (min-width: 1280px) {
            .hf-container { padding-left: 6rem; padding-right: 6rem; }
        }
    </style>
</head>
<body class="bg-stone-100">

<?php if ($includeNav): ?>
    <?php require __DIR__ . '/navbar.php'; ?>
<?php endif; ?>
