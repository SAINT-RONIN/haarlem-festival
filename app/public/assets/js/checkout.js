/**
 * Checkout page interactions:
 * - Payment method selection (radio highlight)
 * - Form validation
 * - Pay button creates Stripe Checkout Session and redirects
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

    var isDisabledByServer = payBtn.hasAttribute('disabled');
    if (isDisabledByServer) return;

    payBtn.addEventListener('click', function () {
        var form = document.getElementById('js-checkout-form');
        if (!form) return;

        clearErrorMessage();

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

        if (!isValid) {
            showErrorMessage('Please complete all required fields.');
            return;
        }

        var selectedMethod = document.querySelector('input[name="paymentMethod"]:checked');
        var saveDetails = document.querySelector('input[name="saveDetails"]');

        var payload = {
            firstName: form.querySelector('input[name="firstName"]').value.trim(),
            lastName: form.querySelector('input[name="lastName"]').value.trim(),
            email: form.querySelector('input[name="email"]').value.trim(),
            paymentMethod: selectedMethod ? selectedMethod.value : 'credit_card',
            saveDetails: saveDetails ? saveDetails.checked : false
        };

        var span = payBtn.querySelector('span');
        var originalLabel = span ? span.textContent : 'Pay';

        payBtn.disabled = true;
        if (span) span.textContent = 'Processing...';

        fetch('/api/checkout/create-session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
            .then(function (response) {
                return response.text().then(function (rawBody) {
                    var data = parseApiJson(rawBody);

                    if (!data) {
                        data = {
                            success: false,
                            error: 'Unexpected server response: ' + rawBody.substring(0, 300)
                        };
                    }

                    return { ok: response.ok, data: data };
                });
            })
            .then(function (result) {
                if (result.ok && result.data.success && result.data.redirectUrl) {
                    window.location.href = result.data.redirectUrl;
                    return;
                }

                var message = result.data && result.data.error ? result.data.error : 'Checkout failed. Please try again.';
                throw new Error(message);
            })
            .catch(function (error) {
                showErrorMessage(error.message || 'Checkout failed. Please try again.');
                payBtn.disabled = false;
                if (span) span.textContent = originalLabel;
            });
    });
}

function parseApiJson(rawBody) {
    try {
        return JSON.parse(rawBody);
    } catch (e) {
        // Recover when server output has extra bytes before/after the JSON payload.
        var trimmed = rawBody.trim();
        var start = trimmed.indexOf('{');
        var end = trimmed.lastIndexOf('}');

        if (start === -1 || end === -1 || end <= start) {
            return null;
        }

        try {
            return JSON.parse(trimmed.substring(start, end + 1));
        } catch (innerError) {
            return null;
        }
    }
}

function showErrorMessage(message) {
    var errorEl = document.getElementById('js-checkout-error');
    if (!errorEl) return;

    errorEl.textContent = message;
    errorEl.classList.remove('hidden');
}

function clearErrorMessage() {
    var errorEl = document.getElementById('js-checkout-error');
    if (!errorEl) return;

    errorEl.textContent = '';
    errorEl.classList.add('hidden');
}
