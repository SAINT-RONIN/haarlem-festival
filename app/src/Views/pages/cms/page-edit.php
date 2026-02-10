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
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userName = $_SESSION['user_display_name'] ?? 'Administrator';
$currentView = 'pages'; // For sidebar highlighting
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
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }

        .char-counter.warning {
            color: #f59e0b;
        }

        .char-counter.error {
            color: #ef4444;
        }
    </style>
</head>
<body class="bg-gray-50">

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
                <a href="/<?= htmlspecialchars($page['slug'] === 'home' ? '' : $page['slug']) ?>"
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
                <?php foreach ($sections as $section): ?>
                    <?php if ($section['isEditable']): ?>
                        <?php require __DIR__ . '/../../partials/cms/edit-section-accordion.php'; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </form>
        </main>
    </section>
</div>

<script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Content limits from PHP
    const contentLimits = <?= json_encode($contentLimits) ?>;
    const imageLimits = <?= json_encode($imageLimits) ?>;
    const pageId = <?= $page['id'] ?>;
    const pageSlug = <?= json_encode($page['slug']) ?>;

    // Initialize TinyMCE for HTML fields
    document.addEventListener('DOMContentLoaded', function () {
        tinymce.init({
            selector: 'textarea[data-tinymce]',
            height: 300,
            menubar: false,
            plugins: 'lists link',
            toolbar: 'undo redo | bold italic underline | bullist numlist | link | removeformat',
            content_style: 'body { font-family: Montserrat, sans-serif; font-size: 14px; line-height: 1.6; }',
            forced_root_block: '',
            force_br_newlines: true,
            convert_newlines_to_brs: true,
            remove_linebreaks: false,
            setup: function (editor) {
                editor.on('keydown', function (e) {
                    if (e.keyCode === 13 && !e.shiftKey) {
                        e.preventDefault();
                        editor.execCommand('InsertLineBreak');
                    }
                });
                editor.on('change keyup', function () {
                    editor.save();
                    updateCharCounter(editor.getElement());
                });
            }
        });

        // Initialize character counters
        document.querySelectorAll('[data-char-limit]').forEach(initCharCounter);

        // Initialize accordion toggles
        document.querySelectorAll('[data-accordion-toggle]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const content = this.closest('.accordion-section').querySelector('.accordion-content');
                const icon = this.querySelector('[data-lucide="chevron-down"]');
                content.classList.toggle('hidden');
                icon.classList.toggle('rotate-180');
            });
        });
    });

    function initCharCounter(input) {
        updateCharCounter(input);
        input.addEventListener('input', function () {
            updateCharCounter(this);
        });
    }

    function updateCharCounter(input) {
        const counterId = input.id + '-counter';
        const counter = document.getElementById(counterId);
        if (!counter) return;

        const type = input.dataset.itemType;
        const maxChars = contentLimits[type] || 500;
        const text = input.value.replace(/<[^>]*>/g, '');
        const currentLength = text.length;

        counter.textContent = currentLength + ' / ' + maxChars;
        counter.classList.remove('warning', 'error');

        if (currentLength > maxChars) {
            counter.classList.add('error');
        } else if (currentLength > maxChars * 0.9) {
            counter.classList.add('warning');
        }
    }

    // Image upload handler
    function uploadImage(itemId, fileInput) {
        const file = fileInput.files[0];
        if (!file) return;

        // Client-side validation
        if (!imageLimits.allowedMimes.includes(file.type)) {
            alert('Invalid file type. Allowed: JPG, PNG, WebP');
            return;
        }

        if (file.size > imageLimits.maxFileSize) {
            alert('File too large. Maximum: ' + imageLimits.maxFileSizeFormatted);
            return;
        }

        const formData = new FormData();
        formData.append('image', file);
        formData.append('item_id', itemId);

        const previewContainer = document.getElementById('preview-' + itemId);
        previewContainer.innerHTML = '<div class="text-gray-500">Uploading...</div>';

        fetch('/cms/pages/' + pageId + '/' + encodeURIComponent(pageSlug) + '/upload-image', {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Server returned ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    previewContainer.innerHTML = '<img src="' + data.filePath + '" class="max-h-40 rounded-lg" alt="Uploaded image">';
                } else {
                    previewContainer.innerHTML = '<div class="text-red-500">' + (data.error || 'Unknown error') + '</div>';
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                previewContainer.innerHTML = '<div class="text-red-500">Upload failed: ' + error.message + '</div>';
            });
    }
</script>
</body>
</html>

