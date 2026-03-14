/**
 * My Program page interactions:
 * - Quantity +/- buttons
 * - Donation input (pay-what-you-like)
 * - Remove item
 * - Clear all items
 *
 * Each program item has TWO DOM elements (desktop grid + mobile card),
 * both share the same data-program-item-id. All mutations sync both.
 */
document.addEventListener('DOMContentLoaded', function () {
    initQuantityButtons();
    initDonationInputs();
    initRemoveButtons();
    initClearButton();
});

function getItemRows(programItemId) {
    return document.querySelectorAll('[data-program-item-id="' + programItemId + '"]');
}

function initQuantityButtons() {
    document.querySelectorAll('.js-qty-decrease').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var row = btn.closest('[data-program-item-id]');
            var qtyEl = row.querySelector('.js-qty-value');
            var current = parseInt(qtyEl.textContent.trim(), 10);
            if (current > 1) {
                updateQuantity(row, current - 1);
            }
        });
    });

    document.querySelectorAll('.js-qty-increase').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var row = btn.closest('[data-program-item-id]');
            var qtyEl = row.querySelector('.js-qty-value');
            var current = parseInt(qtyEl.textContent.trim(), 10);
            updateQuantity(row, current + 1);
        });
    });
}

function updateQuantity(sourceRow, newQuantity) {
    var programItemId = sourceRow.getAttribute('data-program-item-id');

    fetch('/api/program/update-quantity', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ programItemId: parseInt(programItemId, 10), quantity: newQuantity })
    })
        .then(function (response) { return response.json(); })
        .then(function (data) {
            if (data.success) {
                getItemRows(programItemId).forEach(function (row) {
                    row.querySelector('.js-qty-value').textContent = newQuantity;
                    recalculateRowSum(row, newQuantity);
                });
                updatePaymentOverview(data);
            }
        })
        .catch(function (err) {
            console.error('Failed to update quantity:', err);
        });
}

function initDonationInputs() {
    var debounceTimers = {};

    document.querySelectorAll('.js-donation-input').forEach(function (input) {
        input.addEventListener('input', function () {
            var row = input.closest('[data-program-item-id]');
            var programItemId = row.getAttribute('data-program-item-id');

            var donationAmount = parseFloat(input.value) || 0;
            if (donationAmount < 0) {
                donationAmount = 0;
                input.value = '0.00';
            }

            // Sync donation value to sibling row(s) and recalculate
            getItemRows(programItemId).forEach(function (r) {
                var otherInput = r.querySelector('.js-donation-input');
                if (otherInput && otherInput !== input) {
                    otherInput.value = input.value;
                }
                recalculateRowSumWithDonation(r, donationAmount);
            });

            if (debounceTimers[programItemId]) {
                clearTimeout(debounceTimers[programItemId]);
            }

            debounceTimers[programItemId] = setTimeout(function () {
                saveDonation(programItemId, donationAmount);
            }, 500);
        });

        input.addEventListener('blur', function () {
            var val = parseFloat(input.value) || 0;
            input.value = val.toFixed(2);
        });
    });
}

function saveDonation(programItemId, donationAmount) {
    fetch('/api/program/update-donation', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            programItemId: parseInt(programItemId, 10),
            donationAmount: donationAmount
        })
    })
        .then(function (response) { return response.json(); })
        .then(function (data) {
            if (data.success) {
                updatePaymentOverview(data);
            }
        })
        .catch(function (err) {
            console.error('Failed to update donation:', err);
        });
}

function recalculateRowSum(row, newQuantity) {
    var unitPrice = parseFloat(row.getAttribute('data-unit-price')) || 0;
    var donationInput = row.querySelector('.js-donation-input');
    var donation = donationInput ? (parseFloat(donationInput.value) || 0) : 0;
    var sum = (unitPrice * newQuantity) + donation;

    var sumEl = row.querySelector('.js-sum-display span');
    if (sumEl) {
        sumEl.textContent = '€' + sum.toFixed(2);
    }
}

function recalculateRowSumWithDonation(row, donationAmount) {
    var unitPrice = parseFloat(row.getAttribute('data-unit-price')) || 0;
    var qtyEl = row.querySelector('.js-qty-value');
    var quantity = parseInt(qtyEl.textContent.trim(), 10) || 1;
    var sum = (unitPrice * quantity) + donationAmount;

    var sumEl = row.querySelector('.js-sum-display span');
    if (sumEl) {
        sumEl.textContent = '€' + sum.toFixed(2);
    }
}

function initRemoveButtons() {
    document.querySelectorAll('.js-remove-item').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var row = btn.closest('[data-program-item-id]');
            var programItemId = row.getAttribute('data-program-item-id');

            fetch('/api/program/remove', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ programItemId: parseInt(programItemId, 10) })
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (data.success) {
                        // Remove both desktop and mobile rows
                        getItemRows(programItemId).forEach(function (r) {
                            r.remove();
                        });
                        updatePaymentOverview(data);
                        checkEmptyState();
                    }
                })
                .catch(function (err) {
                    console.error('Failed to remove item:', err);
                });
        });
    });
}

function initClearButton() {
    var clearBtn = document.getElementById('js-clear-program');
    if (!clearBtn) return;

    clearBtn.addEventListener('click', function () {
        fetch('/api/program/clear', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data.success) {
                    window.location.reload();
                }
            })
            .catch(function (err) {
                console.error('Failed to clear program:', err);
            });
    });
}

function updatePaymentOverview(data) {
    var subtotalEl = document.querySelector('.js-subtotal');
    var taxEl = document.querySelector('.js-tax');
    var totalEl = document.querySelector('.js-total');
    var checkoutBtn = document.getElementById('js-checkout-btn');

    if (subtotalEl && data.subtotal !== undefined) {
        subtotalEl.textContent = data.subtotal;
    }
    if (taxEl && data.taxAmount !== undefined) {
        taxEl.textContent = data.taxAmount;
    }
    if (totalEl && data.total !== undefined) {
        totalEl.textContent = data.total;
    }
    if (checkoutBtn && data.canCheckout !== undefined) {
        checkoutBtn.disabled = !data.canCheckout;
        var span = checkoutBtn.querySelector('span');
        if (data.canCheckout) {
            checkoutBtn.className = checkoutBtn.className
                .replace('bg-gray-400', 'bg-green-600')
                .replace('cursor-not-allowed', 'cursor-pointer');
            if (checkoutBtn.className.indexOf('hover:bg-green-700') === -1) {
                checkoutBtn.className += ' hover:bg-green-700';
            }
            if (span) span.className = span.className.replace('text-gray-500', 'text-white');
        } else {
            checkoutBtn.className = checkoutBtn.className
                .replace('bg-green-600', 'bg-gray-400')
                .replace('hover:bg-green-700', '')
                .replace('cursor-pointer', 'cursor-not-allowed');
            if (span) span.className = span.className.replace('text-white', 'text-gray-500');
        }
    }
}

function checkEmptyState() {
    var remaining = document.querySelectorAll('[data-program-item-id]');
    if (remaining.length === 0) {
        window.location.reload();
    }
}
