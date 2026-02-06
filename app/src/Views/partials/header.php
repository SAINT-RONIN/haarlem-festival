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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; }
        .font-serif-display { font-family: 'Georgia', serif; }
    </style>
</head>
<body class="bg-stone-100">

<?php if ($includeNav): ?>
    <?php require __DIR__ . '/navbar.php'; ?>
<?php endif; ?>
