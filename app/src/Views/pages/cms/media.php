<?php
/**
 * CMS Media Library page.
 *
 * @var string $currentView
 * @var \App\ViewModels\Cms\CmsMediaLibraryViewModel $viewModel
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Library - Haarlem CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@0.460.0"></script>
</head>
<body class="bg-gray-50 min-h-screen">
<div class="flex min-h-screen">
    <?php require __DIR__ . '/../../partials/cms/sidebar.php'; ?>

    <main class="flex-1 p-8 overflow-auto">
        <!-- Header -->
        <header class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Media Library</h1>
                    <p class="text-gray-600 mt-1">Manage uploaded images</p>
                </div>
                <button
                    type="button"
                    onclick="document.getElementById('media-file-input').click()"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="upload" class="w-5 h-5 mr-2"></i>
                    Upload Image
                </button>
                <input type="file" id="media-file-input" accept="image/*" class="hidden">
            </div>
        </header>

        <?php require __DIR__ . '/../../partials/cms/_flash-messages.php'; ?>

        <!-- Upload Drop Zone -->
        <div id="drop-zone"
             class="mb-6 border-2 border-dashed border-gray-300 rounded-lg p-8 text-center transition-colors cursor-pointer"
             onclick="document.getElementById('media-file-input').click()">
            <i data-lucide="image-plus" class="w-10 h-10 mx-auto text-gray-400 mb-3"></i>
            <p class="text-gray-600 font-medium">Drag and drop an image here, or click to browse</p>
            <p class="text-sm text-gray-400 mt-1">
                Allowed: <?= htmlspecialchars(implode(', ', $viewModel->imageLimits['allowedExtensions'] ?? [])) ?>
                &middot; Max size: <?= htmlspecialchars($viewModel->imageLimits['maxFileSizeFormatted'] ?? '') ?>
                &middot; Max dimensions: <?= htmlspecialchars((string) ($viewModel->imageLimits['maxWidth'] ?? '')) ?>&times;<?= htmlspecialchars((string) ($viewModel->imageLimits['maxHeight'] ?? '')) ?>px
            </p>
        </div>

        <!-- Stats Bar -->
        <div class="mb-4">
            <p id="asset-count-label" class="text-sm text-gray-500">
                <span id="asset-count"><?= count($viewModel->assets) ?></span> image(s) in library
            </p>
        </div>

        <!-- Image Grid -->
        <?php if (empty($viewModel->assets)): ?>
            <div id="media-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                <div class="col-span-full py-16 text-center text-gray-400">
                    <i data-lucide="image-off" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                    <p class="text-base font-medium text-gray-500">No images uploaded yet</p>
                    <p class="text-sm mt-1">Upload your first image using the button above.</p>
                </div>
            </div>
        <?php else: ?>
            <div id="media-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                <?php foreach ($viewModel->assets as $asset): ?>
                    <?php /** @var \App\ViewModels\Cms\CmsMediaListItemViewModel $asset */ ?>
                    <div id="asset-<?= (int) $asset->mediaAssetId ?>" class="bg-white rounded-lg shadow overflow-hidden group relative">
                        <div class="aspect-square bg-gray-100 overflow-hidden">
                            <img
                                src="<?= htmlspecialchars($asset->filePath) ?>"
                                alt="<?= htmlspecialchars($asset->altText) ?>"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="p-2">
                            <p class="text-xs text-gray-700 truncate font-medium" title="<?= htmlspecialchars($asset->originalFileName) ?>">
                                <?= htmlspecialchars($asset->originalFileName) ?>
                            </p>
                            <p class="text-xs text-gray-400">
                                <?= htmlspecialchars($asset->fileSize) ?> &middot; <?= htmlspecialchars($asset->createdAt) ?>
                            </p>
                        </div>
                        <button
                            onclick="deleteAsset(<?= (int) $asset->mediaAssetId ?>)"
                            class="absolute top-2 right-2 w-7 h-7 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                            title="Delete image">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<script>
const csrfToken = '<?= htmlspecialchars($viewModel->csrfToken) ?>';
const imageLimits = <?= json_encode($viewModel->imageLimits) ?>;

function uploadMediaFile(file) {
    if (!imageLimits.allowedMimes.includes(file.type)) {
        alert('Invalid file type. Allowed: ' + (imageLimits.allowedExtensions || []).join(', '));
        return;
    }
    if (file.size > imageLimits.maxFileSize) {
        alert('File too large. Maximum: ' + imageLimits.maxFileSizeFormatted);
        return;
    }

    const formData = new FormData();
    formData.append('image', file);
    formData.append('_csrf', csrfToken);

    const grid = document.getElementById('media-grid');
    const placeholder = document.createElement('div');
    placeholder.id = 'upload-placeholder';
    placeholder.className = 'bg-white rounded-lg shadow overflow-hidden';
    placeholder.innerHTML = '<div class="aspect-square bg-gray-100 flex items-center justify-center"><div class="text-gray-400 text-sm">Uploading...</div></div>';
    grid.prepend(placeholder);

    fetch('/cms/media/upload', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            const el = document.getElementById('upload-placeholder');
            if (data.success) {
                el.className = 'bg-white rounded-lg shadow overflow-hidden group relative';
                el.id = 'asset-' + data.mediaAssetId;
                el.innerHTML = `
                    <div class="aspect-square bg-gray-100 overflow-hidden">
                        <img src="${escapeHtml(data.filePath)}" alt="" class="w-full h-full object-cover">
                    </div>
                    <div class="p-2">
                        <p class="text-xs text-gray-700 truncate font-medium">${escapeHtml(data.originalFileName)}</p>
                        <p class="text-xs text-gray-400">Just now</p>
                    </div>
                    <button onclick="deleteAsset(${data.mediaAssetId})"
                            class="absolute top-2 right-2 w-7 h-7 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                            title="Delete image">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>`;
                updateAssetCount(1);
                if (typeof lucide !== 'undefined') lucide.createIcons();
            } else {
                el.remove();
                alert(data.error || 'Upload failed');
            }
        })
        .catch(() => {
            document.getElementById('upload-placeholder')?.remove();
            alert('Upload failed');
        });
}

function deleteAsset(mediaAssetId) {
    if (!confirm('Are you sure you want to delete this image?')) return;

    const formData = new FormData();
    formData.append('media_asset_id', mediaAssetId);
    formData.append('_csrf', csrfToken);

    fetch('/cms/media/delete', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('asset-' + mediaAssetId)?.remove();
                updateAssetCount(-1);
            } else {
                alert(data.error || 'Delete failed');
            }
        })
        .catch(() => alert('Delete failed'));
}

function updateAssetCount(delta) {
    const countEl = document.getElementById('asset-count');
    const labelEl = document.getElementById('asset-count-label');
    if (countEl && labelEl) {
        const newCount = (parseInt(countEl.textContent) || 0) + delta;
        countEl.textContent = newCount;
        labelEl.textContent = newCount + ' image(s) in library';
    }
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

document.getElementById('media-file-input')?.addEventListener('change', function () {
    if (this.files[0]) {
        uploadMediaFile(this.files[0]);
        this.value = '';
    }
});

const dropZone = document.getElementById('drop-zone');
if (dropZone) {
    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        this.classList.add('border-blue-400', 'bg-blue-50');
    });
    dropZone.addEventListener('dragleave', function (e) {
        e.preventDefault();
        this.classList.remove('border-blue-400', 'bg-blue-50');
    });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        this.classList.remove('border-blue-400', 'bg-blue-50');
        if (e.dataTransfer.files[0]) {
            uploadMediaFile(e.dataTransfer.files[0]);
        }
    });
}
</script>

<script src="/assets/js/cms/cms-common.js"></script>
</body>
</html>
