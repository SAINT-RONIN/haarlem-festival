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

        // Toggle cards
        document.querySelectorAll('[data-cuisines]').forEach(function (card) {
            if (isAll || card.dataset.cuisines.indexOf(value) !== -1) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });

        // Toggle active button state
        document.querySelectorAll('[data-filter-value]').forEach(function (btn) {
            var active = (isAll && btn.dataset.filterValue === 'all') ||
                         (!isAll && btn.dataset.filterValue === value);
            btn.setAttribute('aria-checked', active ? 'true' : 'false');
            if (active) {
                btn.className = btn.className
                    .replace('bg-stone-100', 'bg-red')
                    .replace('text-slate-800', 'text-white');
                if (btn.className.indexOf('bg-red') === -1) {
                    btn.className = btn.className.replace('bg-stone-100', '') + ' bg-red';
                }
            } else {
                btn.className = btn.className
                    .replace('bg-red', 'bg-stone-100')
                    .replace('text-white', 'text-slate-800');
            }
        });
    }
}());
