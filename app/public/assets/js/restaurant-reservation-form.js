/**
 * Restaurant reservation form JavaScript.
 *
 * Handles guest counter interactions and fee total calculation.
 * Requires window.reservationConfig = { reservationFee: <float> } to be set before this script runs.
 */

document.addEventListener('DOMContentLoaded', function () {
    initCounterButtons();
    initDisplays();
});

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
