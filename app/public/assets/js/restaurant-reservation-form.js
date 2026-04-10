/**
 * Restaurant reservation form JavaScript.
 *
 * Handles guest counter interactions and fee total calculation.
 * Requires window.reservationConfig = { reservationFee: <float> } to be set before this script runs.
 */

document.addEventListener('DOMContentLoaded', function () {
    initCounterButtons();
    initDisplays();
    initFormSubmit();
});

function initFormSubmit() {
    var form = document.getElementById('reservation-form-fields');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var submitBtn = form.querySelector('[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success && data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    showFormError(data.errors ? data.errors.join(' ') : 'Something went wrong. Please try again.');
                    if (submitBtn) submitBtn.disabled = false;
                }
            })
            .catch(function () {
                showFormError('Something went wrong. Please try again.');
                if (submitBtn) submitBtn.disabled = false;
            });
    });
}

function showFormError(message) {
    var existing = document.getElementById('reservation-form-error');
    if (existing) {
        existing.textContent = message;
        return;
    }

    var el = document.createElement('p');
    el.id = 'reservation-form-error';
    el.className = 'text-red-600 font-medium mt-2';
    el.textContent = message;

    var form = document.getElementById('reservation-form-fields');
    form.insertBefore(el, form.querySelector('[type="submit"]').parentElement);
}

function initCounterButtons() {
    document.querySelectorAll('[data-counter-action]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = btn.dataset.counterTarget;
            var action   = btn.dataset.counterAction;
            var input    = document.getElementById(targetId);
            var display  = document.getElementById(targetId.replace('_count', '') + '-display');

            var value = parseInt(input.value, 10) || 0;
            if (action === 'increase') value++;
            if (action === 'decrease' && value > 0) value--;

            input.value = value;
            display.textContent = value;
            updateTotal();
        });
    });
}

function initDisplays() {
    ['adults', 'children'].forEach(function (type) {
        var input   = document.getElementById(type + '_count');
        var display = document.getElementById(type + '-display');
        if (input && display) display.textContent = input.value || '0';
    });
    updateTotal();
}

function updateTotal() {
    var reservationFee = (window.reservationConfig || {}).reservationFee || 0;
    var adults   = parseInt(document.getElementById('adults_count').value, 10) || 0;
    var children = parseInt(document.getElementById('children_count').value, 10) || 0;
    var total    = (adults + children) * reservationFee;

    var guestsLabel = document.getElementById('fee-guests-label');
    var breakdown   = document.getElementById('fee-breakdown');
    var feeTotal    = document.getElementById('fee-total');

    if (adults + children === 0) {
        guestsLabel.textContent = '-';
        breakdown.textContent   = 'Select guests to see total';
        feeTotal.textContent    = 'EUR 0.00';
        return;
    }

    var parts = [];
    if (adults > 0)   parts.push(adults + ' Adult' + (adults > 1 ? 's' : ''));
    if (children > 0) parts.push(children + ' Child' + (children > 1 ? 'ren' : ''));

    guestsLabel.textContent = parts.join(' + ');
    breakdown.textContent   = 'EUR ' + reservationFee + ' reservation fee x ' + (adults + children) + ' people';
    feeTotal.textContent    = 'EUR ' + total.toFixed(2).replace('.', ',');
}
