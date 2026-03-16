/**
 * Checkout page interactions:
 * - Payment method selection (radio highlight)
 * - Form validation
 * - Pay button triggers Stripe (placeholder for now)
 */
document.addEventListener('DOMContentLoaded', function () {
    initPaymentMethodHighlight();
    initPayButton();
});

function initPaymentMethodHighlight() {
    var radios = document.querySelectorAll('input[name="paymentMethod"]');
    radios.forEach(function (radio) {
        radio.addEventListener('change', function () {
            radios.forEach(function (r) {
                var label = r.closest('label');
                if (label) {
                    label.classList.remove('outline-slate-800');
                    label.classList.add('outline-gray-200');
                }
            });
            var selected = radio.closest('label');
            if (selected) {
                selected.classList.remove('outline-gray-200');
                selected.classList.add('outline-slate-800');
            }
        });

        // Set initial highlight for checked radio
        if (radio.checked) {
            var label = radio.closest('label');
            if (label) {
                label.classList.remove('outline-gray-200');
                label.classList.add('outline-slate-800');
            }
        }
    });
}

function initPayButton() {
    var payBtn = document.getElementById('js-pay-btn');
    if (!payBtn) return;

    payBtn.addEventListener('click', function () {
        var form = document.getElementById('js-checkout-form');
        if (!form) return;

        // Validate required fields
        var inputs = form.querySelectorAll('input[required]');
        var isValid = true;

        inputs.forEach(function (input) {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('outline-red-400', 'outline-2');
                input.classList.remove('outline-zinc-300', 'outline-1');
            } else {
                input.classList.remove('outline-red-400', 'outline-2');
                input.classList.add('outline-zinc-300', 'outline-1');
            }
        });

        if (!isValid) return;

        var selectedMethod = document.querySelector('input[name="paymentMethod"]:checked');
        var saveDetails = document.querySelector('input[name="saveDetails"]');

        var payload = {
            firstName: form.querySelector('input[name="firstName"]').value.trim(),
            lastName: form.querySelector('input[name="lastName"]').value.trim(),
            email: form.querySelector('input[name="email"]').value.trim(),
            paymentMethod: selectedMethod ? selectedMethod.value : 'credit_card',
            saveDetails: saveDetails ? saveDetails.checked : false
        };

        payBtn.disabled = true;
        var span = payBtn.querySelector('span');
        if (span) span.textContent = 'Processing...';

        fetch('/api/checkout/create-session', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data.success && data.redirectUrl) {
                    window.location.href = data.redirectUrl;
                } else {
                    payBtn.disabled = false;
                    if (span) span.textContent = 'Pay';
                }
            })
            .catch(function () {
                payBtn.disabled = false;
                if (span) span.textContent = 'Pay';
            });
    });
}
