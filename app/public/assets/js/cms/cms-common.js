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

