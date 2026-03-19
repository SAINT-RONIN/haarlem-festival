/**
 * Schedule Filters — server-side filtering via URL query parameters.
 *
 * Clicking a filter option navigates to the same page with updated query params.
 * The server returns only matching events. No client-side show/hide.
 */
(function () {
    'use strict';

    document.querySelectorAll('[data-schedule-filters]').forEach(initFilterSection);

    function initFilterSection(filterContainer) {
        var toggleBtn = filterContainer.querySelector('[data-filter-toggle]');
        var resetBtn = filterContainer.querySelector('[data-filter-reset]');
        var panelId = toggleBtn ? toggleBtn.getAttribute('aria-controls') : null;
        var panel = panelId ? document.getElementById(panelId) : null;
        var closeIcon = toggleBtn ? toggleBtn.querySelector('.filter-close-icon') : null;

        if (!toggleBtn || !panel) return;

        // Toggle panel visibility
        toggleBtn.addEventListener('click', function () {
            var isHidden = panel.classList.toggle('hidden');
            var nowOpen = !isHidden;
            toggleBtn.setAttribute('aria-expanded', String(nowOpen));

            if (resetBtn) resetBtn.classList.toggle('hidden', !nowOpen);
            if (closeIcon) closeIcon.classList.toggle('hidden', !nowOpen);
        });

        // Filter option click — navigate with updated query params
        panel.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-filter-value]');
            if (!btn) return;

            var group = btn.closest('[data-filter-group]');
            if (!group) return;

            var filterKey = group.dataset.filterGroup;
            var filterValue = btn.dataset.filterValue;

            var params = new URLSearchParams(window.location.search);

            if (filterValue === 'all') {
                params.delete(filterKey);
            } else {
                params.set(filterKey, filterValue);
            }

            var queryString = params.toString();
            var newUrl = window.location.pathname + (queryString ? '?' + queryString : '');

            // Scroll to schedule section after navigation
            var sectionId = filterContainer.dataset.scheduleFilters;
            if (sectionId) {
                newUrl += '#' + sectionId;
            }

            window.location.href = newUrl;
        });

        // Reset all filters — navigate to base URL
        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                var sectionId = filterContainer.dataset.scheduleFilters;
                var newUrl = window.location.pathname;
                if (sectionId) {
                    newUrl += '#' + sectionId;
                }
                window.location.href = newUrl;
            });
        }
    }
})();
