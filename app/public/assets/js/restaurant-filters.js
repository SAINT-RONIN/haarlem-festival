/**
 * Restaurant Filters — client-side cuisine filtering.
 *
 * Each card has a data-cuisines attribute (lowercase cuisine string).
 * Clicking a filter button shows only cards whose cuisine matches,
 * or shows all if "All" is selected. Updates URL via pushState.
 */
(function () {
    'use strict';

    document.querySelectorAll('[data-restaurant-filters]').forEach(initFilter);
    window.addEventListener('popstate', applyFromUrl);

    // Apply filter from URL on page load
    applyFromUrl();

    function initFilter(container) {
        var group = container.querySelector('[data-filter-group]');
        if (!group) return;

        group.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-filter-value]');
            if (!btn) return;

            var value = btn.dataset.filterValue;

            // Update URL
            var params = new URLSearchParams(window.location.search);
            if (value === 'all') {
                params.delete('cuisine');
            } else {
                params.set('cuisine', value);
            }
            var qs = params.toString();
            history.pushState(null, '', window.location.pathname + (qs ? '?' + qs : ''));

            applyFilter(value);
        });
    }

    function applyFromUrl() {
        var cuisine = new URLSearchParams(window.location.search).get('cuisine');
        applyFilter(cuisine || 'all');
    }

    function applyFilter(value) {
        var isAll = !value || value === 'all';

        document.querySelectorAll('[data-cuisines]').forEach(function (card) {
            var cuisines = (card.dataset.cuisines || '')
                .split('|')
                .map(function (tag) { return tag.trim(); })
                .filter(Boolean);

            if (isAll || cuisines.indexOf(value) !== -1) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });

        document.querySelectorAll('[data-filter-value]').forEach(function (btn) {
            var active = (isAll && btn.dataset.filterValue === 'all') ||
                         (!isAll && btn.dataset.filterValue === value);

            btn.setAttribute('aria-checked', active ? 'true' : 'false');
            btn.classList.toggle('bg-red', active);
            btn.classList.toggle('text-white', active);
            btn.classList.toggle('hover:bg-royal-blue', active);
            btn.classList.toggle('bg-stone-100', !active);
            btn.classList.toggle('text-slate-800', !active);
            btn.classList.toggle('hover:bg-red', !active);
            btn.classList.toggle('hover:text-white', !active);
        });
    }
}());
