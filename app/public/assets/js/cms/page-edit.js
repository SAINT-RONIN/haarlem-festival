/**
 * CMS Page Edit JavaScript.
 *
 * Handles page content editing functionality including:
 * - TinyMCE initialization
 * - Character counters
 * - Accordion toggles
 * - Image uploads
 */

// Configuration passed from PHP (must be set before this script loads)
// Expected globals: contentLimits, imageLimits, pageId, pageSlug

/**
 * Initialize TinyMCE for HTML editor fields.
 */
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
    if (typeof imageLimits === 'undefined' || typeof pageId === 'undefined' || typeof pageSlug === 'undefined') {
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

