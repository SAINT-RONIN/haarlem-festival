/**
 * Schedule Filters — AJAX-based server-side filtering.
 *
 * Clicking a filter fetches the updated schedule HTML from the server
 * and swaps it into the DOM without a full page reload.
 * URL updates via pushState so back/forward and bookmarks work.
 */
(function () {
    'use strict';

    document.querySelectorAll('[data-schedule-filters]').forEach(initFilterSection);
    window.addEventListener('popstate', handlePopState);

    function initFilterSection(filterContainer) {
        var sectionId = filterContainer.dataset.scheduleFilters;
        var toggleBtn = filterContainer.querySelector('[data-filter-toggle]');
        var resetBtn = filterContainer.querySelector('[data-filter-reset]');
        var panelId = toggleBtn ? toggleBtn.getAttribute('aria-controls') : null;
        var panel = panelId ? document.getElementById(panelId) : null;
        var closeIcon = toggleBtn ? toggleBtn.querySelector('.filter-close-icon') : null;

        if (!toggleBtn || !panel) return;

        // Toggle panel visibility (stays client-side)
        toggleBtn.addEventListener('click', function () {
            var isHidden = panel.classList.toggle('hidden');
            var nowOpen = !isHidden;
            toggleBtn.setAttribute('aria-expanded', String(nowOpen));
            if (resetBtn) resetBtn.classList.toggle('hidden', !nowOpen);
            if (closeIcon) closeIcon.classList.toggle('hidden', !nowOpen);
        });

        // Filter option click — AJAX fetch
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

            // Update URL without reload
            history.pushState({ scheduleFilters: true }, '', newUrl);

            // Fetch filtered schedule
            fetchSchedule(sectionId, params);
        });

        // Reset all filters
        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                history.pushState({ scheduleFilters: true }, '', window.location.pathname);
                fetchSchedule(sectionId, new URLSearchParams());
            });
        }
    }

    function fetchSchedule(sectionId, params) {
        var section = document.getElementById(sectionId);
        if (!section) return;

        var pageSlug = sectionId.replace(/-schedule$/, '');
        var queryString = params.toString();
        var apiUrl = '/api/schedule/' + pageSlug + (queryString ? '?' + queryString : '');

        // Loading state
        section.style.opacity = '0.5';
        section.style.pointerEvents = 'none';

        fetch(apiUrl, {
            headers: { 'Accept': 'text/html' }
        })
        .then(function (response) {
            if (!response.ok) throw new Error('Filter request failed');
            return response.text();
        })
        .then(function (html) {
            // Parse the response and extract the section element
            var temp = document.createElement('div');
            temp.innerHTML = html;
            var newSection = temp.querySelector('section') || temp.firstElementChild;

            if (newSection) {
                section.parentNode.replaceChild(newSection, section);

                // Re-initialize filter listeners on new DOM
                var newFilterContainer = newSection.querySelector('[data-schedule-filters]');
                if (newFilterContainer) {
                    initFilterSection(newFilterContainer);
                }

                // Re-initialize add-to-program buttons
                if (typeof initAddToProgramButtons === 'function') {
                    initAddToProgramButtons();
                }

                // Re-initialize Lucide icons in the new content
                if (typeof lucide !== 'undefined' && lucide.createIcons) {
                    lucide.createIcons();
                }
            }
        })
        .catch(function (err) {
            console.error('Schedule filter error:', err);
            section.style.opacity = '1';
            section.style.pointerEvents = '';
        });
    }

    function handlePopState() {
        var filterContainer = document.querySelector('[data-schedule-filters]');
        if (!filterContainer) return;

        var sectionId = filterContainer.dataset.scheduleFilters;
        var params = new URLSearchParams(window.location.search);
        fetchSchedule(sectionId, params);
    }
})();
