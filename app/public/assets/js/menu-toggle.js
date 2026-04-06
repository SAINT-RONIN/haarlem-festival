/**
 * Mobile menu toggle functions for hero and navbar.
 *
 * Uses data attributes for event binding to avoid inline JavaScript.
 * - data-toggle-menu="<menu-id>" on buttons
 * - data-scroll-top on scroll-to-top links
 */

if (!window.__menuToggleBootstrapRegistered) {
    window.__menuToggleBootstrapRegistered = true;

/**
 * Initialize all menu toggles and scroll handlers on DOM ready.
 */
document.addEventListener('DOMContentLoaded', function () {
    if (window.__menuToggleInitialized) {
        return;
    }
    window.__menuToggleInitialized = true;

    initMenuToggles();
    initScrollToTop();
});
}

/**
 * Initializes menu toggle buttons.
 */
function initMenuToggles() {
    var buttons = document.querySelectorAll('[data-toggle-menu]');
    buttons.forEach(function (btn) {
        if (btn.dataset.menuToggleBound === '1') {
            return;
        }
        btn.dataset.menuToggleBound = '1';

        btn.addEventListener('click', function () {
            var menuId = btn.getAttribute('data-toggle-menu');
            toggleMenuById(menuId, btn);
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
    var button = document.getElementById('hero-menu-btn');
    toggleMenuById('hero-nav-menu', button);
}

/**
 * Toggles the standard navbar menu (used on non-hero pages).
 */
function toggleNavMenu() {
    var button = document.getElementById('nav-menu-btn');
    toggleMenuById('nav-menu', button);
}

/**
 * Toggles any menu by its element id.
 */
function toggleMenuById(menuId, button) {
    if (!menuId) {
        return;
    }

    var menu = document.getElementById(menuId);
    if (!menu) {
        return;
    }

    var bars = resolveMenuBars(button);
    var isOpen = !menu.classList.contains('hidden');

    if (isOpen) {
        closeMenu(menu, bars);
        if (button) {
            button.setAttribute('aria-expanded', 'false');
        }
    } else {
        openMenu(menu, bars);
        if (button) {
            button.setAttribute('aria-expanded', 'true');
        }
    }
}

/**
 * Resolves animated icon bars based on button id (hero/nav).
 */
function resolveMenuBars(button) {
    var empty = {bar1: null, bar2: null, bar3: null};
    if (!button || !button.id) {
        return empty;
    }

    var prefix = button.id.replace(/-menu-btn$/, '');
    if (!prefix) {
        return empty;
    }

    return {
        bar1: document.getElementById(prefix + '-bar-1'),
        bar2: document.getElementById(prefix + '-bar-2'),
        bar3: document.getElementById(prefix + '-bar-3')
    };
}

/**
 * Closes a menu with animation.
 */
function closeMenu(menu, bars) {
    menu.classList.add('opacity-0', '-translate-y-2');
    menu.classList.remove('opacity-100', 'translate-y-0');

    if (bars.bar1 && bars.bar2 && bars.bar3) {
        bars.bar1.classList.remove('rotate-45', 'top-1/2', '-translate-y-1/2');
        bars.bar1.classList.add('top-0');
        bars.bar2.classList.remove('opacity-0');
        bars.bar3.classList.remove('-rotate-45', 'top-1/2', '-translate-y-1/2');
        bars.bar3.classList.add('bottom-0');
    }

    setTimeout(function () {
        menu.classList.add('hidden');
        menu.classList.remove('flex');
    }, 300);
}

/**
 * Opens a menu with animation.
 */
function openMenu(menu, bars) {
    menu.classList.remove('hidden');
    menu.classList.add('flex');

    menu.offsetHeight;

    menu.classList.remove('opacity-0', '-translate-y-2');
    menu.classList.add('opacity-100', 'translate-y-0');

    if (bars.bar1 && bars.bar2 && bars.bar3) {
        bars.bar1.classList.add('rotate-45', 'top-1/2', '-translate-y-1/2');
        bars.bar1.classList.remove('top-0');
        bars.bar2.classList.add('opacity-0');
        bars.bar3.classList.add('-rotate-45', 'top-1/2', '-translate-y-1/2');
        bars.bar3.classList.remove('bottom-0');
    }
}
