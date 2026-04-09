<?php
/**
 * Minimal CMS shell for the login page — no sidebar, centered card layout.
 *
 * Rendered by pages/cms/login.php. Does not use CmsPageLayout because login
 * has different chrome (royal-blue background, no sidebar, Google Fonts).
 *
 * @var \App\View\ViewTemplate $content  The login body partial + its locals.
 */

use App\View\ViewRenderer;
use App\View\ViewTemplate;

$content ??= new ViewTemplate('');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Login | Haarlem Festival</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">
    <link rel="icon" href="/assets/Icons/Logo.svg" type="image/svg+xml" sizes="any">
    <link rel="stylesheet" href="/assets/css/tokens.css">
</head>
<body class="bg-royal-blue min-h-screen flex flex-col items-center justify-center px-4 py-12">
<a href="#cms-login-main"
   class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-white focus:text-royal-blue focus:rounded-lg focus:shadow focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2">
    Skip to main content
</a>

<?php ViewRenderer::render($content->path, $content->locals); ?>
</body>
</html>
