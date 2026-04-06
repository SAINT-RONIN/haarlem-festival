/**
 * CMS Media Library JavaScript.
 *
 * Handles image upload, delete, and drag-and-drop.
 * Requires window.mediaConfig to be set before this script runs:
 *   window.mediaConfig = { csrfToken: '...', imageLimits: {...} }
 */

document.addEventListener('DOMContentLoaded', function () {
    initFileInput();
    initDropZone();
    initDeleteButtons();
    if (typeof lucide !== 'undefined') lucide.createIcons();
});

function initFileInput() {
    var input = document.getElementById('media-file-input');
    if (!input) return;
    input.addEventListener('change', function () {
        if (this.files[0]) {
            uploadMediaFile(this.files[0]);
            this.value = '';
        }
    });

    document.querySelectorAll('[data-action="triggerUpload"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            input.click();
        });
    });
}

function initDropZone() {
    var dropZone = document.getElementById('drop-zone');
    if (!dropZone) return;
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
    dropZone.addEventListener('click', function () {
        document.getElementById('media-file-input').click();
    });
}

function initDeleteButtons() {
    document.getElementById('media-grid')?.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-delete-asset]');
        if (!btn) return;
        deleteAsset(parseInt(btn.dataset.deleteAsset, 10));
    });
}

function uploadMediaFile(file) {
    var config = window.mediaConfig || {};
    var imageLimits = config.imageLimits || {};

    if (imageLimits.allowedMimes && !imageLimits.allowedMimes.includes(file.type)) {
        alert('Invalid file type. Allowed: ' + (imageLimits.allowedExtensions || []).join(', '));
        return;
    }
    if (imageLimits.maxFileSize && file.size > imageLimits.maxFileSize) {
        alert('File too large. Maximum: ' + imageLimits.maxFileSizeFormatted);
        return;
    }

    var formData = new FormData();
    formData.append('image', file);
    formData.append('_csrf', config.csrfToken || '');

    var grid = document.getElementById('media-grid');
    var placeholder = document.createElement('div');
    placeholder.id = 'upload-placeholder';
    placeholder.className = 'bg-white rounded-lg shadow overflow-hidden';
    placeholder.innerHTML = '<div class="aspect-square bg-gray-100 flex items-center justify-center"><div class="text-gray-400 text-sm">Uploading...</div></div>';
    grid.prepend(placeholder);

    fetch('/cms/media/upload', { method: 'POST', body: formData })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            var el = document.getElementById('upload-placeholder');
            if (data.success) {
                el.className = 'bg-white rounded-lg shadow overflow-hidden group relative';
                el.id = 'asset-' + data.mediaAssetId;
                el.innerHTML =
                    '<div class="aspect-square bg-gray-100 overflow-hidden">' +
                        '<img src="' + escapeHtml(data.filePath) + '" alt="" class="w-full h-full object-cover">' +
                    '</div>' +
                    '<div class="p-2">' +
                        '<p class="text-xs text-gray-700 truncate font-medium">' + escapeHtml(data.originalFileName) + '</p>' +
                        '<p class="text-xs text-gray-400">Just now</p>' +
                    '</div>' +
                    '<button data-delete-asset="' + data.mediaAssetId + '"' +
                        ' class="absolute top-2 right-2 w-7 h-7 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"' +
                        ' title="Delete image">' +
                        '<i data-lucide="trash-2" class="w-4 h-4"></i>' +
                    '</button>';
                updateAssetCount(1);
                if (typeof lucide !== 'undefined') lucide.createIcons();
            } else {
                el.remove();
                alert(data.error || 'Upload failed');
            }
        })
        .catch(function () {
            document.getElementById('upload-placeholder')?.remove();
            alert('Upload failed');
        });
}

function deleteAsset(mediaAssetId) {
    if (!confirm('Are you sure you want to delete this image?')) return;

    var config = window.mediaConfig || {};
    var formData = new FormData();
    formData.append('media_asset_id', mediaAssetId);
    formData.append('_csrf', config.csrfToken || '');

    fetch('/cms/media/delete', { method: 'POST', body: formData })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                document.getElementById('asset-' + mediaAssetId)?.remove();
                updateAssetCount(-1);
            } else {
                alert(data.error || 'Delete failed');
            }
        })
        .catch(function () { alert('Delete failed'); });
}

function updateAssetCount(delta) {
    var countEl = document.getElementById('asset-count');
    var labelEl = document.getElementById('asset-count-label');
    if (countEl && labelEl) {
        var newCount = (parseInt(countEl.textContent) || 0) + delta;
        countEl.textContent = newCount;
        labelEl.textContent = newCount + ' image(s) in library';
    }
}

function escapeHtml(str) {
    var div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}
