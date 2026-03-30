/**
 * Retry payment page — payment method selection and Stripe redirect.
 */
(function () {
    'use strict';

    var selectedMethod = null;
    var payBtn = document.getElementById('btn-retry-pay');
    var errorEl = document.getElementById('retry-error');
    var methodButtons = document.querySelectorAll('.js-payment-method');

    // ── Payment Method Selection ──

    methodButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            selectedMethod = btn.dataset.method;
            highlightSelectedMethod(btn);
            enablePayButton();
        });
    });

    function highlightSelectedMethod(activeBtn) {
        methodButtons.forEach(function (btn) {
            btn.classList.remove('border-slate-800', 'bg-slate-50');
            btn.classList.add('border-gray-200');
        });
        activeBtn.classList.remove('border-gray-200');
        activeBtn.classList.add('border-slate-800', 'bg-slate-50');
    }

    function enablePayButton() {
        payBtn.disabled = false;
        payBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
        payBtn.classList.add('bg-green-600', 'hover:bg-green-700', 'cursor-pointer');
        payBtn.textContent = 'Pay Now';
    }

    // ── Pay Button ──

    payBtn.addEventListener('click', function () {
        if (!selectedMethod || payBtn.disabled) return;

        payBtn.disabled = true;
        payBtn.textContent = 'Processing...';
        hideError();

        fetch('/api/checkout/retry-session', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                orderId: RETRY_ORDER_ID,
                paymentMethod: selectedMethod
            })
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success && data.redirectUrl) {
                window.location.href = data.redirectUrl;
            } else {
                showError(data.error || 'Payment could not be initiated. Please try again.');
                payBtn.disabled = false;
                payBtn.textContent = 'Pay Now';
            }
        })
        .catch(function () {
            showError('Network error. Please check your connection and try again.');
            payBtn.disabled = false;
            payBtn.textContent = 'Pay Now';
        });
    });

    // ── Helpers ──

    function showError(message) {
        errorEl.textContent = message;
        errorEl.classList.remove('hidden');
    }

    function hideError() {
        errorEl.classList.add('hidden');
    }
})();
