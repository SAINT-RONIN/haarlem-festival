<?php

declare(strict_types=1);

$errorMessage = $errorMessage ?? 'Unexpected error occurred.';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error</title>
</head>
<body>
<main>
    <h1>Something went wrong</h1>
    <p><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
</main>
</body>
</html>
