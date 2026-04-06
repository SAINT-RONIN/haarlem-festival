/**
 * CMS Venues JavaScript.
 *
 * Handles venue creation via AJAX on the venues management page.
 */

document.addEventListener('DOMContentLoaded', function () {
    initAddVenueButton();
    if (typeof lucide !== 'undefined') lucide.createIcons();
});

function initAddVenueButton() {
    var addBtn = document.getElementById('addVenueBtn');
    if (!addBtn) return;
    addBtn.addEventListener('click', addVenue);
}

async function addVenue() {
    var nameInput  = document.getElementById('NewVenueName');
    var addressInput = document.getElementById('NewVenueAddress');
    var errorEl   = document.getElementById('venueError');
    var successEl = document.getElementById('venueSuccess');

    var name    = nameInput.value.trim();
    var address = addressInput.value.trim();

    errorEl.classList.add('hidden');
    successEl.classList.add('hidden');

    if (!name) {
        errorEl.textContent = 'Venue name is required';
        errorEl.classList.remove('hidden');
        return;
    }

    try {
        var response = await fetch('/cms/venues', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'VenueName=' + encodeURIComponent(name) + '&AddressLine=' + encodeURIComponent(address)
        });
        var data = await response.json();

        if (data.success) {
            nameInput.value = '';
            addressInput.value = '';
            window.location.reload();
        } else {
            errorEl.textContent = data.errors ? data.errors.join(', ') : 'Failed to create venue';
            errorEl.classList.remove('hidden');
        }
    } catch (err) {
        errorEl.textContent = 'An error occurred. Please try again.';
        errorEl.classList.remove('hidden');
    }
}
