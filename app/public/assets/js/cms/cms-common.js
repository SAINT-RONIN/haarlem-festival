/**
 * CMS Common JavaScript utilities.
 *
 * Shared functionality across all CMS pages.
 */

/**
 * Initialize Lucide icons on the page.
 * Call this after DOM is ready or after dynamic content loads.
 */
function initLucideIcons() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

// Auto-initialize on DOM ready
document.addEventListener('DOMContentLoaded', initLucideIcons);

/**
 * Delegated submit handler for forms with a data-confirm attribute.
 * Shows a confirm dialog before the form submits; cancels if the user declines.
 * Usage: <form data-confirm="Are you sure?">
 */
document.addEventListener('submit', function (e) {
    var form = e.target;
    var message = form.dataset.confirm;
    if (message && !confirm(message)) {
        e.preventDefault();
    }
}, true);

/**
 * Delegated click handler for CMS image-picker actions.
 * Reads data-action on the clicked element and calls the matching function.
 */
document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-action]');
        if (!btn) return;
        var action = btn.dataset.action;
        if (action === 'openEventImagePicker') openEventImagePicker();
        if (action === 'clearEventImage')      clearEventImage();
        if (action === 'openJazzCardImagePicker') openJazzCardImagePicker();
        if (action === 'clearJazzCardImage')      clearJazzCardImage();
        if (action === 'openMediaLibrary') {
            var itemId = parseInt(btn.dataset.itemId, 10);
            if (typeof openMediaLibrary === 'function') openMediaLibrary(itemId);
        }
    });

    document.addEventListener('change', function (e) {
        var input = e.target;
        if (input.dataset.action !== 'uploadImage') return;
        var itemId = parseInt(input.dataset.itemId, 10);
        if (typeof uploadImage === 'function') uploadImage(itemId, input);
    });
});

/**
 * Opens a media library modal for selecting an event featured image.
 * Sets the hidden FeaturedImageAssetId input and updates the preview.
 */
function openEventImagePicker() {
    var existing = document.getElementById('eventImagePickerModal');
    if (existing) existing.remove();

    var modal = document.createElement('div');
    modal.id = 'eventImagePickerModal';
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
    modal.innerHTML = [
        '<div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl flex flex-col" style="max-height:80vh">',
            '<div class="flex items-center justify-between p-4 border-b border-gray-200">',
                '<h3 class="text-lg font-semibold text-gray-900">Select Featured Image</h3>',
                '<button type="button" onclick="closeEventImagePicker()" class="p-1 text-gray-400 hover:text-gray-600">✕</button>',
            '</div>',
            '<div id="eventImageGrid" class="flex-1 overflow-y-auto p-4">',
                '<div class="text-center text-gray-400 py-8">Loading...</div>',
            '</div>',
        '</div>'
    ].join('');
    document.body.appendChild(modal);

    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeEventImagePicker();
    });

    fetch('/api/cms/media')
        .then(function (r) { return r.json(); })
        .then(function (data) {
            var grid = document.getElementById('eventImageGrid');
            if (!grid) return;
            if (!data.success || !data.assets || !data.assets.length) {
                grid.innerHTML = '<div class="text-center text-gray-400 py-8">No images in library. Upload from the Media page first.</div>';
                return;
            }
            var cards = data.assets.map(function (asset) {
                return '<button type="button" onclick="selectEventImage(' + asset.mediaAssetId + ', \'' + asset.filePath.replace(/'/g, "\\'") + '\')" ' +
                    'class="group rounded-lg overflow-hidden border-2 border-transparent hover:border-blue-500 transition-colors text-left w-full">' +
                    '<div class="aspect-square bg-gray-100 overflow-hidden">' +
                    '<img src="' + asset.filePath + '" alt="" class="w-full h-full object-cover">' +
                    '</div>' +
                    '<p class="p-1.5 text-xs text-gray-600 truncate">' + (asset.originalFileName || '') + '</p>' +
                    '</button>';
            });
            grid.innerHTML = '<div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">' + cards.join('') + '</div>';
        })
        .catch(function () {
            var grid = document.getElementById('eventImageGrid');
            if (grid) grid.innerHTML = '<div class="text-center text-red-500 py-8">Failed to load media library.</div>';
        });
}

function closeEventImagePicker() {
    var modal = document.getElementById('eventImagePickerModal');
    if (modal) modal.remove();
}

function selectEventImage(assetId, filePath) {
    var input = document.getElementById('FeaturedImageAssetId');
    var preview = document.getElementById('featuredImagePreview');
    if (input) input.value = assetId;
    if (preview) {
        preview.innerHTML = '<img src="' + filePath + '" alt="Featured image" class="w-full h-full object-cover">';
    }
    var clearBtn = document.getElementById('clearImageBtn');
    if (clearBtn) clearBtn.classList.remove('hidden');
    var noText = document.getElementById('noImageText');
    if (noText) noText.remove();
    closeEventImagePicker();
}

function clearEventImage() {
    var input = document.getElementById('FeaturedImageAssetId');
    var preview = document.getElementById('featuredImagePreview');
    if (input) input.value = '';
    if (preview) {
        preview.innerHTML = '<span id="noImageText" class="text-gray-400 text-xs text-center px-2">No image</span>';
    }
    var clearBtn = document.getElementById('clearImageBtn');
    if (clearBtn) clearBtn.classList.add('hidden');
}

/**
 * Opens a media library modal for selecting a Jazz lineup card image.
 * Sets the hidden imageAssetId input and updates the preview.
 */
function openJazzCardImagePicker() {
    var existing = document.getElementById('jazzCardImagePickerModal');
    if (existing) existing.remove();

    var modal = document.createElement('div');
    modal.id = 'jazzCardImagePickerModal';
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
    modal.innerHTML = [
        '<div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl flex flex-col" style="max-height:80vh">',
            '<div class="flex items-center justify-between p-4 border-b border-gray-200">',
                '<h3 class="text-lg font-semibold text-gray-900">Select Lineup Image</h3>',
                '<button type="button" onclick="closeJazzCardImagePicker()" class="p-1 text-gray-400 hover:text-gray-600">&#10005;</button>',
            '</div>',
            '<div id="jazzCardImageGrid" class="flex-1 overflow-y-auto p-4">',
                '<div class="text-center text-gray-400 py-8">Loading...</div>',
            '</div>',
        '</div>'
    ].join('');
    document.body.appendChild(modal);

    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeJazzCardImagePicker();
    });

    fetch('/api/cms/media')
        .then(function (r) { return r.json(); })
        .then(function (data) {
            var grid = document.getElementById('jazzCardImageGrid');
            if (!grid) return;
            if (!data.success || !data.assets || !data.assets.length) {
                grid.innerHTML = '<div class="text-center text-gray-400 py-8">No images in library. Upload from the <a href="/cms/media" class="text-blue-500 underline">Media page</a> first.</div>';
                return;
            }
            var cards = data.assets.map(function (asset) {
                return '<button type="button" onclick="selectJazzCardImage(' + asset.mediaAssetId + ', \'' + asset.filePath.replace(/'/g, "\\'") + '\')" ' +
                    'class="group rounded-lg overflow-hidden border-2 border-transparent hover:border-blue-500 transition-colors text-left w-full">' +
                    '<div class="aspect-square bg-gray-100 overflow-hidden">' +
                    '<img src="' + asset.filePath + '" alt="" class="w-full h-full object-cover">' +
                    '</div>' +
                    '<p class="p-1.5 text-xs text-gray-600 truncate">' + (asset.originalFileName || '') + '</p>' +
                    '</button>';
            });
            grid.innerHTML = '<div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">' + cards.join('') + '</div>';
        })
        .catch(function () {
            var grid = document.getElementById('jazzCardImageGrid');
            if (grid) grid.innerHTML = '<div class="text-center text-red-500 py-8">Failed to load media library.</div>';
        });
}

function closeJazzCardImagePicker() {
    var modal = document.getElementById('jazzCardImagePickerModal');
    if (modal) modal.remove();
}

function selectJazzCardImage(assetId, filePath) {
    var input = document.getElementById('imageAssetId');
    var preview = document.getElementById('jazzCardImagePreview');
    if (input) input.value = assetId;
    if (preview) {
        preview.innerHTML = '<img src="' + filePath + '" alt="Lineup image" class="w-32 h-24 rounded-lg border border-gray-200 object-cover">';
    }
    closeJazzCardImagePicker();
}

function clearJazzCardImage() {
    var input = document.getElementById('imageAssetId');
    var preview = document.getElementById('jazzCardImagePreview');
    if (input) input.value = '';
    if (preview) {
        preview.innerHTML = '<div class="w-32 h-24 bg-gray-100 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300"><svg class="w-6 h-6 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg></div>';
    }
    // Remove the clear button
    var clearBtn = document.querySelector('[data-action="clearJazzCardImage"]');
    if (clearBtn) clearBtn.remove();
}

/**
 * Placeholder SVG for image preview when no image is set.
 */
function jazzCardImagePlaceholder() {
    return '<div class="w-32 h-24 bg-gray-100 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300"><svg class="w-6 h-6 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg></div>';
}

/**
 * Jazz lineup card form — artist selector auto-fill and form validation.
 *
 * When the user picks an artist from the dropdown, the hidden name, style,
 * description, and image fields are populated automatically.
 * Form submission is blocked when no artist has been selected.
 */
document.addEventListener('DOMContentLoaded', function () {
    var select = document.getElementById('artistSelect');
    if (!select) return;

    select.addEventListener('change', function () {
        var option = select.options[select.selectedIndex];
        var nameInput = document.getElementById('name');
        var styleInput = document.getElementById('style');
        var descInput = document.getElementById('cardDescription');
        var imageIdInput = document.getElementById('imageAssetId');
        var imagePreview = document.getElementById('jazzCardImagePreview');

        if (!option || !option.value) {
            if (nameInput) nameInput.value = '';
            if (styleInput) styleInput.value = '';
            if (descInput) descInput.value = '';
            if (imageIdInput) imageIdInput.value = '';
            if (imagePreview) imagePreview.innerHTML = jazzCardImagePlaceholder();
            return;
        }

        if (nameInput) nameInput.value = option.dataset.name || '';
        if (styleInput) styleInput.value = option.dataset.style || '';
        if (descInput && !descInput.value.trim()) descInput.value = option.dataset.description || '';

        var imgAssetId = option.dataset.imageAssetId;
        var imgUrl = option.dataset.imageUrl;
        if (imageIdInput) imageIdInput.value = imgAssetId || '';
        if (imagePreview) {
            if (imgUrl) {
                imagePreview.innerHTML = '<img src="' + imgUrl + '" alt="Artist image" class="w-32 h-24 rounded-lg border border-gray-200 object-cover">';
            } else {
                imagePreview.innerHTML = jazzCardImagePlaceholder();
            }
        }
    });

    // Prevent form submission when no artist is selected
    var form = document.getElementById('jazzLineupCardForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            var nameInput = document.getElementById('name');
            if (nameInput && !nameInput.value.trim()) {
                e.preventDefault();
                select.focus();
                select.classList.add('border-red-500');
                alert('Please select an artist.');
            }
        });
    }
});

