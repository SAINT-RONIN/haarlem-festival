/**
 * Restaurant cuisine filter — client-side, no page reload.
 *
 * Every restaurant card is already on the page. Clicking a filter button shows the
 * cards whose cuisine matches and hides the rest. Nothing navigates, so the page
 * never reloads and the scroll position never moves.
 */
document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('[data-filter]');
    const cards = document.querySelectorAll('[data-cuisines]');

    buttons.forEach((button) => {
        button.addEventListener('click', () => {
            const selected = button.getAttribute('data-filter'); // '' means "All"

            // Show the cards that match, hide the others.
            cards.forEach((card) => {
                const tags = card.getAttribute('data-cuisines').split(',');
                const matches = selected === '' || tags.includes(selected);
                card.classList.toggle('hidden', !matches);
            });

            // Move the "active" highlight to the clicked button.
            buttons.forEach((other) => {
                other.classList.remove('bg-red', 'text-white');
                other.classList.add('bg-stone-100', 'text-slate-800');
            });
            button.classList.remove('bg-stone-100', 'text-slate-800');
            button.classList.add('bg-red', 'text-white');
        });
    });
});