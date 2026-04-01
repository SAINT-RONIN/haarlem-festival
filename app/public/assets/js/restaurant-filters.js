/**
 * Restaurant Filters — AJAX-based server-side filtering.
 *
 * Clicking a cuisine filter fetches the updated restaurant cards HTML from
 * the server and swaps it into the DOM without a full page reload.
 * URL updates via pushState so back/forward and bookmarks work.
 *
 * Follows the same pattern as schedule-filters.js.
 */
(function () {
    'use strict';

    document.querySelectorAll('[data-restaurant-filters]').forEach(initRestaurantFilter);
    window.addEventListener('popstate', handlePopState);

    function initRestaurantFilter(filterContainer) {
        var panel = filterContainer.querySelector('[data-filter-group]');
        if (!panel) return;

        panel.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-filter-value]');
            if (!btn) return;

            var filterValue = btn.dataset.filterValue;

            var params = new URLSearchParams(window.location.search);
            if (filterValue === 'all') {
                params.delete('cuisine');
            } else {
                params.set('cuisine', filterValue);
            }

            var queryString = params.toString();
            var newUrl = window.location.pathname + (queryString ? '?' + queryString : '');

            history.pushState({ restaurantFilters: true }, '', newUrl);

            fetchRestaurants(params);
        });
    }

    function fetchRestaurants(params) {
        var section = document.getElementById('restaurants-grid');
        if (!section) return;

        var queryString = params.toString();
        var apiUrl = '/api/restaurants' + (queryString ? '?' + queryString : '');

        section.style.opacity = '0.5';
        section.style.pointerEvents = 'none';

        fetch(apiUrl, { headers: { 'Accept': 'text/html' } })
            .then(function (response) {
                if (!response.ok) throw new Error('Filter request failed');
                return response.text();
            })
            .then(function (html) {
                var temp = document.createElement('div');
                temp.innerHTML = html;
                var newSection = temp.querySelector('section') || temp.firstElementChild;

                if (newSection) {
                    section.parentNode.replaceChild(newSection, section);

                    var newFilter = newSection.querySelector('[data-restaurant-filters]');
                    if (newFilter) initRestaurantFilter(newFilter);

                    if (typeof lucide !== 'undefined' && lucide.createIcons) {
                        lucide.createIcons();
                    }
                }
            })
            .catch(function (err) {
                console.error('Restaurant filter error:', err);
                section.style.opacity = '1';
                section.style.pointerEvents = '';
            });
    }

    function handlePopState() {
        var params = new URLSearchParams(window.location.search);
        fetchRestaurants(params);
    }
}());
