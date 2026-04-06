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

