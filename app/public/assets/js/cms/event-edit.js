/**
 * CMS Event Edit JavaScript.
 *
 * Handles event editing functionality including:
 * - Session form toggle
 *
 * Uses data attributes for event binding to avoid inline JavaScript.
 */

/**
 * Initialize event listeners on DOM ready.
 */
document.addEventListener('DOMContentLoaded', function () {
    initToggleButtons();
    initLucide();
});

/**
 * Initialize toggle buttons.
 */
function initToggleButtons() {
    document.querySelectorAll('[data-toggle="addSessionForm"]').forEach(function (btn) {
        btn.addEventListener('click', toggleAddSession);
    });
}

/**
 * Initialize Lucide icons.
 */
function initLucide() {
    if (typeof initLucideIcons === 'function') {
        initLucideIcons();
    } else if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

/**
 * Toggle the add session form visibility.
 */
function toggleAddSession() {
    const form = document.getElementById('addSessionForm');
    form.classList.toggle('hidden');
}

