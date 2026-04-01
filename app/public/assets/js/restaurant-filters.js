(function () {
    'use strict';

    var activeFilter = 'all';

    document.querySelectorAll('[data-filter-group="cuisine"]').forEach(function (group) {
        group.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-filter-value]');
            if (!btn) return;

            activeFilter = btn.dataset.filterValue;

            group.querySelectorAll('[data-filter-value]').forEach(function (b) {
                var isActive = b.dataset.filterValue === activeFilter;
                b.setAttribute('aria-checked', isActive ? 'true' : 'false');
                b.classList.toggle('bg-red',        isActive);
                b.classList.toggle('text-white',    isActive);
                b.classList.toggle('bg-stone-100',  !isActive);
                b.classList.toggle('text-slate-800', !isActive);
            });

            document.querySelectorAll('[data-cuisines]').forEach(function (card) {
                if (activeFilter === 'all') {
                    card.style.display = '';
                } else {
                    var cuisines = card.dataset.cuisines.split(',').map(function (c) { return c.trim(); });
                    card.style.display = cuisines.indexOf(activeFilter) === -1 ? 'none' : '';
                }
            });
        });
    });
}());
