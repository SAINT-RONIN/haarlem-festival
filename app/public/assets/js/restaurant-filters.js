/**
 * Restaurant cuisine filter.
 *
 * Each card has a data-cuisines attribute with pipe-separated lowercase tags.
 * Clicking a filter button shows only matching cards (or all).
 */
document.querySelectorAll('[data-filter-value]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var value = btn.dataset.filterValue;

        // Show/hide cards
        document.querySelectorAll('[data-cuisines]').forEach(function (card) {
            card.style.display =
                value === 'all' || card.dataset.cuisines.indexOf(value) !== -1
                    ? ''
                    : 'none';
        });

        // Toggle active button style
        document.querySelectorAll('[data-filter-value]').forEach(function (b) {
            var isActive = b === btn;
            b.setAttribute('aria-checked', isActive ? 'true' : 'false');
            b.classList.toggle('bg-red', isActive);
            b.classList.toggle('text-white', isActive);
            b.classList.toggle('bg-stone-100', !isActive);
            b.classList.toggle('text-slate-800', !isActive);
        });
    });
});
