/**
 * Schedule Filters — shared client-side filtering for schedule sections.
 *
 * Uses data attributes on event cards and day columns to filter visible events
 * based on user-selected filter options (day, time range, price type, venue, etc.).
 */
(function () {
    'use strict';

    document.querySelectorAll('[data-schedule-filters]').forEach(initFilterSection);

    function initFilterSection(filterContainer) {
        var sectionId = filterContainer.dataset.scheduleFilters;
        var toggleBtn = filterContainer.querySelector('[data-filter-toggle]');
        var resetBtn = filterContainer.querySelector('[data-filter-reset]');
        var panelId = toggleBtn ? toggleBtn.getAttribute('aria-controls') : null;
        var panel = panelId ? document.getElementById(panelId) : null;
        var closeIcon = toggleBtn ? toggleBtn.querySelector('.filter-close-icon') : null;
        var section = document.getElementById(sectionId);
        var countEl = filterContainer.querySelector('[data-filter-event-count]');

        if (!toggleBtn || !panel || !section) return;

        // Toggle panel
        toggleBtn.addEventListener('click', function () {
            var isOpen = panel.classList.toggle('hidden');
            var nowOpen = !isOpen;
            toggleBtn.setAttribute('aria-expanded', String(nowOpen));

            if (resetBtn) resetBtn.classList.toggle('hidden', !nowOpen);
            if (closeIcon) closeIcon.classList.toggle('hidden', !nowOpen);
        });

        // Filter option click
        panel.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-filter-value]');
            if (!btn) return;

            var group = btn.closest('[data-filter-group]');
            if (!group) return;

            // Deselect all in group, select clicked
            group.querySelectorAll('[data-filter-value]').forEach(function (opt) {
                var isSelected = opt === btn;
                opt.setAttribute('aria-checked', String(isSelected));
                opt.classList.toggle('bg-pink-700', isSelected);
                opt.classList.toggle('text-stone-100', isSelected);
                opt.classList.toggle('bg-stone-100', !isSelected);
                opt.classList.toggle('text-slate-800', !isSelected);
                opt.classList.toggle('hover:bg-stone-200', !isSelected);
            });

            applyFilters(section, panel, countEl);
        });

        // Reset all filters
        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                panel.querySelectorAll('[data-filter-group]').forEach(function (group) {
                    group.querySelectorAll('[data-filter-value]').forEach(function (opt) {
                        var isDefault = opt.dataset.filterValue === 'all';
                        opt.setAttribute('aria-checked', String(isDefault));
                        opt.classList.toggle('bg-pink-700', isDefault);
                        opt.classList.toggle('text-stone-100', isDefault);
                        opt.classList.toggle('bg-stone-100', !isDefault);
                        opt.classList.toggle('text-slate-800', !isDefault);
                        opt.classList.toggle('hover:bg-stone-200', !isDefault);
                    });
                });

                applyFilters(section, panel, countEl);
            });
        }
    }

    function applyFilters(section, panel, countEl) {
        // Collect active filters
        var activeFilters = {};
        panel.querySelectorAll('[data-filter-group]').forEach(function (group) {
            var key = group.dataset.filterGroup;
            var selected = group.querySelector('[aria-checked="true"]');
            var value = selected ? selected.dataset.filterValue : 'all';
            if (value !== 'all') {
                activeFilters[key] = value;
            }
        });

        // Day filter: show/hide entire day columns
        var dayColumns = section.querySelectorAll('[data-filter-day]');
        dayColumns.forEach(function (col) {
            if (activeFilters.day) {
                col.style.display = col.dataset.filterDay === activeFilters.day ? '' : 'none';
            } else {
                col.style.display = '';
            }
        });

        // Card-level filters
        var cards = section.querySelectorAll('article[data-filter-time-range]');
        cards.forEach(function (card) {
            // Skip cards in hidden day columns
            var dayCol = card.closest('[data-filter-day]');
            if (dayCol && dayCol.style.display === 'none') return;

            var visible = true;

            // Time range filter
            if (activeFilters.timeRange && card.dataset.filterTimeRange !== activeFilters.timeRange) {
                visible = false;
            }

            // Price type filter
            if (visible && activeFilters.priceType && card.dataset.filterPriceType !== activeFilters.priceType) {
                visible = false;
            }

            // Venue filter
            if (visible && activeFilters.venue && card.dataset.filterVenue !== activeFilters.venue) {
                visible = false;
            }

            // Language filter — check label badges in the card
            if (visible && activeFilters.language) {
                var hasMatch = false;
                card.querySelectorAll('[data-label-text]').forEach(function (lbl) {
                    if (lbl.dataset.labelText.toLowerCase() === activeFilters.language) {
                        hasMatch = true;
                    }
                });
                if (!hasMatch) {
                    card.querySelectorAll('.schedule-label-badge').forEach(function (badge) {
                        if (badge.textContent.trim().toLowerCase() === activeFilters.language) {
                            hasMatch = true;
                        }
                    });
                }
                if (!hasMatch) visible = false;
            }

            // Age filter
            if (visible && activeFilters.age) {
                var minAge = parseInt(card.dataset.filterAge, 10) || 0;
                var filterAge = parseInt(activeFilters.age, 10);
                if (minAge < filterAge) {
                    visible = false;
                }
            }

            // Apply visibility via the wrapping <li>
            var listItem = card.closest('li');
            if (listItem) {
                listItem.style.display = visible ? '' : 'none';
            }
        });

        // Hide day columns where all cards are now hidden
        dayColumns.forEach(function (col) {
            if (col.style.display === 'none') return;
            var visibleCards = col.querySelectorAll('li:not([style*="display: none"]) article');
            if (visibleCards.length === 0) {
                col.style.display = 'none';
            }
        });

        // Update event count
        if (countEl) {
            var totalVisible = 0;
            dayColumns.forEach(function (col) {
                if (col.style.display === 'none') return;
                totalVisible += col.querySelectorAll('li:not([style*="display: none"]) article').length;
            });
            var labelWord = countEl.textContent.trim().replace(/^\d+\s*/, '');
            countEl.textContent = totalVisible + ' ' + labelWord;
        }
    }
})();
