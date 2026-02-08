/**
 * Initialize Lucide icons after page load.
 */
if (typeof lucide !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            lucide.createIcons();
        });
    } else {
        lucide.createIcons();
    }
}
