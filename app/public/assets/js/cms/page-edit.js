function initTinyMCE() {
    if (typeof tinymce === 'undefined') {
        console.warn('TinyMCE not loaded');
        return;
    }

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
}

/**
 * Initialize a character counter for an input element.
 * @param {HTMLElement} input - The input element with data-char-limit attribute
 */
function initCharCounter(input) {
    updateCharCounter(input);
    input.addEventListener('input', function () {
        updateCharCounter(this);
    });
}

/**
 * Update the character counter display for an input.
 * @param {HTMLElement} input - The input element
 */
function updateCharCounter(input) {
    if (typeof contentLimits === 'undefined') return;

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

/**
 * Initialize accordion toggle functionality.
 */
function initAccordionToggles() {
    document.querySelectorAll('[data-accordion-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const content = this.closest('.accordion-section').querySelector('.accordion-content');
            const icon = this.querySelector('[data-lucide="chevron-down"]');
            content.classList.toggle('hidden');
            if (icon) {
                icon.classList.toggle('rotate-180');
            }
        });
    });
}

/**
 * Upload an image for a CMS item.
 * @param {number} itemId - The CMS item ID
 * @param {HTMLInputElement} fileInput - The file input element
 */
function uploadImage(itemId, fileInput) {
    if (typeof imageLimits === 'undefined' || typeof pageId === 'undefined' || typeof pageSlug === 'undefined' || typeof csrfToken === 'undefined') {
        console.error('Required configuration not set');
        return;
    }

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
    formData.append('csrf_token', csrfToken);

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
                previewContainer.innerHTML = '<img src="' + escapeAttr(data.filePath) + '" class="max-h-40 rounded-lg" alt="Uploaded image">';
            } else {
                previewContainer.innerHTML = '<div class="text-red-500">' + escapeHtml(data.error || 'Unknown error') + '</div>';
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            previewContainer.innerHTML = '<div class="text-red-500">Upload failed: ' + error.message + '</div>';
        });
}

/**
 * Escape HTML for safe insertion into innerHTML.
 * @param {string} str - Raw string to escape
 * @returns {string} HTML-escaped string
 */
function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

/**
 * Escape a string for safe use inside an HTML attribute value.
 * @param {string} str - Raw string to escape
 * @returns {string} Attribute-safe escaped string
 */
function escapeAttr(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

/**
 * Opens the media library modal for selecting an existing image.
 * @param {number} itemId - The CMS item ID to link the selected image to
 */
function openMediaLibrary(itemId) {
    let modal = document.getElementById('media-library-modal');
    if (modal) modal.remove();

    modal = document.createElement('div');
    modal.id = 'media-library-modal';
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = [
        '<div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[80vh] flex flex-col mx-4">',
            '<div class="flex items-center justify-between p-4 border-b border-gray-200">',
                '<h2 class="text-lg font-semibold text-gray-900">Select from Media Library</h2>',
                '<button type="button" onclick="closeMediaLibrary()" class="p-1 text-gray-400 hover:text-gray-600 rounded transition-colors">',
                    '<i data-lucide="x" class="w-5 h-5"></i>',
                '</button>',
            '</div>',
            '<div id="media-library-grid" class="flex-1 overflow-y-auto p-4">',
                '<div class="text-center text-gray-400 py-8">Loading...</div>',
            '</div>',
        '</div>'
    ].join('');
    document.body.appendChild(modal);

    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeMediaLibrary();
    });

    document.addEventListener('keydown', function handleEscape(e) {
        if (e.key === 'Escape') {
            closeMediaLibrary();
            document.removeEventListener('keydown', handleEscape);
        }
    });

    if (typeof lucide !== 'undefined') lucide.createIcons();

    fetch('/api/cms/media')
        .then(function (r) { return r.json(); })
        .then(function (data) {
            const grid = document.getElementById('media-library-grid');
            if (!grid) return;

            if (!data.success || !data.assets || data.assets.length === 0) {
                grid.innerHTML = '<div class="text-center text-gray-400 py-8">No images in library. Upload images from the Media page first.</div>';
                return;
            }

            const cards = data.assets.map(function (asset) {
                return [
                    '<button type="button"',
                    '    onclick="selectMediaAsset(' + asset.mediaAssetId + ', \'' + escapeAttr(asset.filePath) + '\', ' + itemId + ')"',
                    '    class="group rounded-lg overflow-hidden border-2 border-transparent hover:border-blue-500 transition-colors focus:outline-none focus:border-blue-500 text-left w-full">',
                    '    <div class="aspect-square bg-gray-100 overflow-hidden">',
                    '        <img src="' + escapeAttr(asset.filePath) + '" alt="' + escapeAttr(asset.originalFileName) + '" class="w-full h-full object-cover">',
                    '    </div>',
                    '    <div class="p-1.5">',
                    '        <p class="text-xs text-gray-600 truncate">' + escapeHtml(asset.originalFileName) + '</p>',
                    '    </div>',
                    '</button>'
                ].join('');
            });

            grid.innerHTML = '<div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">' + cards.join('') + '</div>';
        })
        .catch(function () {
            const grid = document.getElementById('media-library-grid');
            if (grid) {
                grid.innerHTML = '<div class="text-center text-red-500 py-8">Failed to load media library.</div>';
            }
        });
}

/**
 * Closes and removes the media library modal.
 */
function closeMediaLibrary() {
    const modal = document.getElementById('media-library-modal');
    if (modal) modal.remove();
}

/**
 * Links an existing media asset to a CMS item via the upload endpoint.
 * @param {number} mediaAssetId - The media asset ID to link
 * @param {string} filePath - The file path for updating the preview
 * @param {number} itemId - The CMS item ID to link the asset to
 */
function selectMediaAsset(mediaAssetId, filePath, itemId) {
    if (typeof pageId === 'undefined' || typeof pageSlug === 'undefined' || typeof csrfToken === 'undefined') {
        console.error('Required page configuration not set');
        return;
    }

    closeMediaLibrary();

    const previewContainer = document.getElementById('preview-' + itemId);
    if (previewContainer) {
        previewContainer.innerHTML = '<div class="text-gray-500">Linking...</div>';
    }

    const formData = new FormData();
    formData.append('item_id', itemId);
    formData.append('media_asset_id', mediaAssetId);
    formData.append('csrf_token', csrfToken);

    fetch('/cms/pages/' + pageId + '/' + encodeURIComponent(pageSlug) + '/upload-image', {
        method: 'POST',
        body: formData
    })
        .then(function (response) {
            if (!response.ok) throw new Error('Server returned ' + response.status);
            return response.json();
        })
        .then(function (data) {
            if (!previewContainer) return;
            if (data.success) {
                previewContainer.innerHTML = '<img src="' + escapeAttr(filePath) + '" class="max-h-40 rounded-lg" alt="Selected image">';
            } else {
                previewContainer.innerHTML = '<div class="text-red-500">' + escapeHtml(data.error || 'Failed to link image') + '</div>';
            }
        })
        .catch(function (error) {
            console.error('Link error:', error);
            if (previewContainer) {
                previewContainer.innerHTML = '<div class="text-red-500">Failed to link image.</div>';
            }
        });
}

/**
 * Initialize all page edit functionality.
 */
function initPageEdit() {
    initTinyMCE();
    document.querySelectorAll('[data-char-limit]').forEach(initCharCounter);
    initAccordionToggles();

    // Re-initialize Lucide icons if available
    if (typeof initLucideIcons === 'function') {
        initLucideIcons();
    } else if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

// Auto-initialize on DOM ready
document.addEventListener('DOMContentLoaded', initPageEdit);

