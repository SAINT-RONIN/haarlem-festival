<?php
/**
 * CMS Page Edit - Admin page content editor.
 *
 * @var array $page Page info (id, title, slug)
 * @var array $sections Sections with items
 * @var array $contentLimits Character limits per type
 * @var array $imageLimits Image dimension/size limits
 * @var string|null $successMessage Flash success message
 * @var string|null $errorMessage Flash error message
 * @var string $userName Admin user display name (passed from controller)
 */

$currentView = 'pages'; // For sidebar highlighting
$userName = $userName ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit <?= htmlspecialchars($page['title']) ?> | CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- TinyMCE (CDN, keyless/community build) -->
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">
    <link rel="icon" href="/assets/Icons/Logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="/assets/css/tokens.css">
    <link rel="stylesheet" href="/assets/css/cms.css">
</head>
<body class="bg-gray-50 cms-body">

<div class="flex h-screen">
    <?php require __DIR__ . '/../../partials/cms/sidebar.php'; ?>

    <section class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="/cms/pages"
                   class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Edit: <?= htmlspecialchars($page['title']) ?></h1>
                    <p class="text-sm text-gray-500">Slug: /<?= htmlspecialchars($page['slug']) ?></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="<?= htmlspecialchars($previewUrl ?? ('/' . ($page['slug'] === 'home' ? '' : $page['slug']))) ?>"
                   target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                    Preview
                </a>
                <button type="submit" form="page-edit-form"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Save Changes
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-6">
            <?php if ($successMessage): ?>
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    <?= htmlspecialchars($successMessage) ?>
                </div>
            <?php endif; ?>

            <?php if ($errorMessage): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>

            <form id="page-edit-form" action="/cms/pages/<?= $page['id'] ?>/<?= htmlspecialchars($page['slug']) ?>/edit"
                  method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                <?php foreach ($sections as $section): ?>
                    <?php if ($section['isEditable']): ?>
                        <?php require __DIR__ . '/../../partials/cms/edit-section-accordion.php'; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </form>
        </main>
    </section>
</div>

<!-- Configuration for JS -->
<script>
    const contentLimits = <?= json_encode($contentLimits) ?>;
    const imageLimits = <?= json_encode($imageLimits) ?>;
    const pageId = <?= $page['id'] ?>;
    const pageSlug = <?= json_encode($page['slug']) ?>;
    const csrfToken = <?= json_encode($csrfToken ?? '') ?>;
</script>
<script src="/assets/js/cms/cms-common.js"></script>
<script src="/assets/js/cms/page-edit.js"></script>
</body>
</html>

