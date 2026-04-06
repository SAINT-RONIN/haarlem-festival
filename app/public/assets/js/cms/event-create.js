/**
 * CMS Event Create JavaScript.
 *
 * Handles event creation functionality including:
 * - New venue creation via AJAX
 * - Form toggle functionality
 *
 * Uses data attributes for event binding to avoid inline JavaScript.
 */

/**
 * Initialize event listeners on DOM ready.
 */
document.addEventListener('DOMContentLoaded', function () {
    initToggleButtons();
    initCreateVenueButton();
    initEventTypeVisibility();
    initLucide();
});

/**
 * Initialize toggle buttons.
 */
function initToggleButtons() {
    document.querySelectorAll('[data-toggle="newVenueForm"]').forEach(function (btn) {
        btn.addEventListener('click', toggleNewVenueForm);
    });
}

/**
 * Initialize create venue button.
 */
function initCreateVenueButton() {
    document.querySelectorAll('[data-action="createVenue"]').forEach(function (btn) {
        btn.addEventListener('click', createVenue);
    });
}

/**
 * Initialize Lucide icons.
 */
function initLucide() {
    if (typeof initLucideIcons === 'function') {
        initLucideIcons();
    } else if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

/**
 * Toggle the new venue form visibility.
 */
function toggleNewVenueForm() {
    const form = document.getElementById('newVenueForm');
    form.classList.toggle('hidden');
    if (!form.classList.contains('hidden')) {
        document.getElementById('NewVenueName').focus();
    }
}

/**
 * Create a new venue via AJAX.
 */
async function createVenue() {
    const name = document.getElementById('NewVenueName').value.trim();
    const address = document.getElementById('NewVenueAddress').value.trim();
    const errorEl = document.getElementById('venueError');

    if (!name) {
        errorEl.textContent = 'Venue name is required';
        errorEl.classList.remove('hidden');
        return;
    }

    errorEl.classList.add('hidden');

    try {
        const response = await fetch('/cms/venues', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `VenueName=${encodeURIComponent(name)}&AddressLine=${encodeURIComponent(address)}`
        });

        const data = await response.json();

        if (data.success) {
            // Add new venue to dropdown and select it
            const select = document.getElementById('VenueId');
            const option = document.createElement('option');
            option.value = data.venueId;
            option.textContent = data.name + (address ? ' - ' + address : '');
            option.selected = true;
            select.appendChild(option);

            // Clear and hide the form
            document.getElementById('NewVenueName').value = '';
            document.getElementById('NewVenueAddress').value = '';
            toggleNewVenueForm();
        } else {
            errorEl.textContent = data.errors ? data.errors.join(', ') : 'Failed to create venue';
            errorEl.classList.remove('hidden');
        }
    } catch (error) {
        errorEl.textContent = 'An error occurred. Please try again.';
        errorEl.classList.remove('hidden');
    }
}

/**
 * Toggle field visibility based on selected event type.
 * Reads JAZZ_TYPE and RESTAURANT_TYPE from data attributes on the form element.
 */
function initEventTypeVisibility() {
    var form = document.querySelector('form[data-jazz-type-id]');
    if (!form) return;

    var typeSelect          = document.getElementById('EventTypeId');
    var artistField         = document.getElementById('artistField');
    var starsField          = document.getElementById('starsField');
    var cuisineField        = document.getElementById('cuisineField');
    var restaurantDescField = document.getElementById('restaurantDescField');
    var featuredImageField  = document.getElementById('featuredImageField');

    var JAZZ_TYPE       = parseInt(form.dataset.jazzTypeId, 10);
    var RESTAURANT_TYPE = parseInt(form.dataset.restaurantTypeId, 10);

    function updateVisibility() {
        var val = parseInt(typeSelect.value, 10);
        var isRestaurant = val === RESTAURANT_TYPE;
        artistField.classList.toggle('hidden', val !== JAZZ_TYPE);
        starsField.classList.toggle('hidden', !isRestaurant);
        cuisineField.classList.toggle('hidden', !isRestaurant);
        restaurantDescField.classList.toggle('hidden', !isRestaurant);
        featuredImageField.classList.toggle('hidden', !isRestaurant);
    }

    typeSelect.addEventListener('change', updateVisibility);
    updateVisibility();
}


