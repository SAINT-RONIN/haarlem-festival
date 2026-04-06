/**
 * CMS Event Edit JavaScript.
 *
 * Handles event editing functionality including:
 * - Session form toggle
 * - Venue creation via AJAX
 *
 * Uses data attributes for event binding to avoid inline JavaScript.
 */

/**
 * Initialize event listeners on DOM ready.
 */
document.addEventListener('DOMContentLoaded', function () {
    initToggleButtons();
    initCreateVenueButton();
    initLucide();
});

/**
 * Initialize toggle buttons for session form and venue form.
 */
function initToggleButtons() {
    document.querySelectorAll('[data-toggle="addSessionForm"]').forEach(function (btn) {
        btn.addEventListener('click', toggleAddSession);
    });
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
 * Toggle the add session form visibility.
 */
function toggleAddSession() {
    var form = document.getElementById('addSessionForm');
    form.classList.toggle('hidden');
}

/**
 * Toggle the new venue form visibility.
 */
function toggleNewVenueForm() {
    var form = document.getElementById('newVenueForm');
    form.classList.toggle('hidden');
    if (!form.classList.contains('hidden')) {
        document.getElementById('NewVenueName').focus();
    }
}

/**
 * Create a new venue via AJAX.
 */
async function createVenue() {
    var name = document.getElementById('NewVenueName').value.trim();
    var address = document.getElementById('NewVenueAddress').value.trim();
    var errorEl = document.getElementById('venueError');

    if (!name) {
        errorEl.textContent = 'Venue name is required';
        errorEl.classList.remove('hidden');
        return;
    }

    errorEl.classList.add('hidden');

    try {
        var response = await fetch('/cms/venues', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'VenueName=' + encodeURIComponent(name) + '&AddressLine=' + encodeURIComponent(address)
        });

        var data = await response.json();

        if (data.success) {
            var select = document.getElementById('VenueId');
            var option = document.createElement('option');
            option.value = data.venueId;
            option.textContent = data.name + (address ? ' - ' + address : '');
            option.selected = true;
            select.appendChild(option);

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

