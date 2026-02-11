/**
 * Mobile menu toggle functions for hero and navbar.
 *
 * Uses data attributes for event binding to avoid inline JavaScript.
 * - data-toggle-menu="<menu-id>" on buttons
 * - data-scroll-top on scroll-to-top links
 */

/**
 * Initialize all menu toggles and scroll handlers on DOM ready.
 */
document.addEventListener('DOMContentLoaded', function () {
    initMenuToggles();
    initScrollToTop();
});

/**
 * Initializes menu toggle buttons.
 */
function initMenuToggles() {
    var buttons = document.querySelectorAll('[data-toggle-menu]');
    buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var menuId = btn.getAttribute('data-toggle-menu');
            if (menuId === 'hero-nav-menu') {
                toggleHeroMenu();
            } else if (menuId === 'nav-menu') {
                toggleNavMenu();
            }
        });
    });
}

/**
 * Initializes scroll-to-top links.
 */
function initScrollToTop() {
    var links = document.querySelectorAll('[data-scroll-top]');
    links.forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            window.scrollTo({top: 0, behavior: 'smooth'});
        });
    });
}

/**
 * Toggles the hero navigation menu (used on homepage).
 */
function toggleHeroMenu() {
    var menu = document.getElementById('hero-nav-menu');
    var bar1 = document.getElementById('hero-bar-1');
    var bar2 = document.getElementById('hero-bar-2');
    var bar3 = document.getElementById('hero-bar-3');

    var isOpen = !menu.classList.contains('hidden');

    if (isOpen) {
        closeMenu(menu, bar1, bar2, bar3);
    } else {
        openMenu(menu, bar1, bar2, bar3);
    }
}

/**
 * Toggles the standard navbar menu (used on non-hero pages).
 */
function toggleNavMenu() {
    var menu = document.getElementById('nav-menu');
    var bar1 = document.getElementById('nav-bar-1');
    var bar2 = document.getElementById('nav-bar-2');
    var bar3 = document.getElementById('nav-bar-3');

    var isOpen = !menu.classList.contains('hidden');

    if (isOpen) {
        closeMenu(menu, bar1, bar2, bar3);
    } else {
        openMenu(menu, bar1, bar2, bar3);
    }
}

/**
 * Closes a menu with animation.
 */
function closeMenu(menu, bar1, bar2, bar3) {
    menu.classList.add('opacity-0', '-translate-y-2');
    menu.classList.remove('opacity-100', 'translate-y-0');

    bar1.classList.remove('rotate-45', 'top-1/2', '-translate-y-1/2');
    bar1.classList.add('top-0');
    bar2.classList.remove('opacity-0');
    bar3.classList.remove('-rotate-45', 'top-1/2', '-translate-y-1/2');
    bar3.classList.add('bottom-0');

    setTimeout(function () {
        menu.classList.add('hidden');
        menu.classList.remove('flex');
    }, 300);
}

/**
 * Opens a menu with animation.
 */
function openMenu(menu, bar1, bar2, bar3) {
    menu.classList.remove('hidden');
    menu.classList.add('flex');

    menu.offsetHeight;

    menu.classList.remove('opacity-0', '-translate-y-2');
    menu.classList.add('opacity-100', 'translate-y-0');

    bar1.classList.add('rotate-45', 'top-1/2', '-translate-y-1/2');
    bar1.classList.remove('top-0');
    bar2.classList.add('opacity-0');
    bar3.classList.add('-rotate-45', 'top-1/2', '-translate-y-1/2');
    bar3.classList.remove('bottom-0');
}
