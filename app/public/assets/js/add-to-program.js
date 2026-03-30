/**
 * Add-to-program button interactions for schedule cards.
 * Transforms the button into a counter widget, then posts to the API on confirm.
 */
document.addEventListener('DOMContentLoaded', function () {
    initAddToProgramButtons();
    initAddPassToCartButtons();
});

function initAddToProgramButtons() {
    document.querySelectorAll('[data-event-session-id]:not([data-atp-init])').forEach(function (btn) {
        btn.setAttribute('data-atp-init', '1');
        btn.addEventListener('click', function () {
            showCounterWidget(btn);
        });
    });
}

window.initAddToProgramButtons = initAddToProgramButtons;

// Reusable builder for ticket counter rows (single or group)
function buildTicketCounterRow(initialQuantity) {
    var quantity = initialQuantity || 1;

    var row = document.createElement('div');
    row.className = 'inline-flex items-center gap-2';

    var decreaseBtn = document.createElement('button');
    decreaseBtn.type = 'button';
    decreaseBtn.className = 'w-7 h-7 bg-slate-800 rounded-[5px] flex items-center justify-center hover:bg-slate-700 transition-colors text-white text-lg font-bold';
    decreaseBtn.textContent = '\u2212';
    decreaseBtn.setAttribute('aria-label', 'Decrease quantity');

    var qtyDisplay = document.createElement('span');
    qtyDisplay.className = 'w-8 h-7 bg-stone-100 rounded flex items-center justify-center text-slate-800 text-sm font-semibold';
    qtyDisplay.textContent = String(quantity);

    var increaseBtn = document.createElement('button');
    increaseBtn.type = 'button';
    increaseBtn.className = 'w-7 h-7 bg-slate-800 rounded-[5px] flex items-center justify-center hover:bg-slate-700 transition-colors text-white text-lg font-bold';
    increaseBtn.textContent = '+';
    increaseBtn.setAttribute('aria-label', 'Increase quantity');

    var cancelBtn = document.createElement('button');
    cancelBtn.type = 'button';
    cancelBtn.className = 'w-7 h-7 rounded-[5px] flex items-center justify-center text-slate-500 hover:text-red-500 transition-colors text-lg font-bold';
    cancelBtn.textContent = '\u00D7';
    cancelBtn.setAttribute('aria-label', 'Cancel selection');

    row.appendChild(decreaseBtn);
    row.appendChild(qtyDisplay);
    row.appendChild(increaseBtn);
    row.appendChild(cancelBtn);

    return {
        row: row,
        decreaseBtn: decreaseBtn,
        increaseBtn: increaseBtn,
        cancelBtn: cancelBtn,
        qtyDisplay: qtyDisplay,
        getQuantity: function () {
            return quantity;
        },
        setQuantity: function (newQty) {
            quantity = newQty;
            qtyDisplay.textContent = String(quantity);
        }
    };
}

function showCounterWidget(originalBtn) {
    var sessionId = parseInt(originalBtn.getAttribute('data-event-session-id'), 10);
    var container = originalBtn.parentElement;
    var isHistoryEvent = originalBtn.getAttribute('data-is-history-event') === '1';
    var confirmText = originalBtn.getAttribute('data-confirm-text') || 'Confirm selection';
    var addingText = originalBtn.getAttribute('data-adding-text') || 'Adding...';
    var successText = originalBtn.getAttribute('data-success-text') || 'Added to program';

    var quantity = 1;
    var groupTicketQuantity = 1;

    originalBtn.style.display = 'none';

    // Main widget wrapper (column layout)
    var widget = document.createElement('div');
    widget.className = 'flex flex-col items-end gap-2';

    // Column of counters (each section stacked vertically)
    var countersWrapper = document.createElement('div');
    countersWrapper.className = 'flex flex-col items-end gap-2';

    // Single tickets section
    if (isHistoryEvent) {
        var singleLabel = document.createElement('span');
        singleLabel.className = 'text-xs text-slate-700 font-semibold self-end';
        singleLabel.textContent = 'Single tickets';
        countersWrapper.appendChild(singleLabel);
    }

    var mainCounter = buildTicketCounterRow(quantity);
    countersWrapper.appendChild(mainCounter.row);

    // Group tickets section (history only)
    var groupCounter = null;
    if (isHistoryEvent) {
        var groupLabel = document.createElement('span');
        groupLabel.className = 'text-xs text-slate-700 font-semibold self-end mt-1';
        groupLabel.textContent = 'Group tickets';

        groupCounter = buildTicketCounterRow(groupTicketQuantity);

        countersWrapper.appendChild(groupLabel);
        countersWrapper.appendChild(groupCounter.row);
    }

    // Single shared confirm button
    var confirmBtn = document.createElement('button');
    confirmBtn.type = 'button';
    confirmBtn.className = 'px-3.5 py-2 rounded-[10px] bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition-colors duration-200';
    confirmBtn.textContent = confirmText;

    widget.appendChild(countersWrapper);
    widget.appendChild(confirmBtn);
    container.appendChild(widget);

    // Single tickets counter wiring
    mainCounter.decreaseBtn.addEventListener('click', function () {
        var current = mainCounter.getQuantity();
        if (current > 1) {
            mainCounter.setQuantity(current - 1);
        }
    });

    mainCounter.increaseBtn.addEventListener('click', function () {
        mainCounter.setQuantity(mainCounter.getQuantity() + 1);
    });

    mainCounter.cancelBtn.addEventListener('click', function () {
        widget.remove();
        originalBtn.style.display = '';
    });

    // Group tickets counter wiring
    if (groupCounter) {
        groupCounter.decreaseBtn.addEventListener('click', function () {
            var current = groupCounter.getQuantity();
            if (current > 1) {
                groupCounter.setQuantity(current - 1);
            }
        });

        groupCounter.increaseBtn.addEventListener('click', function () {
            groupCounter.setQuantity(groupCounter.getQuantity() + 1);
        });

        groupCounter.cancelBtn.addEventListener('click', function () {
            // Reset group tickets to 1 but keep widget open
            groupCounter.setQuantity(1);
        });
    }

    // Confirm handler (single button for both counters)
    confirmBtn.addEventListener('click', function () {
        confirmBtn.disabled = true;
        confirmBtn.textContent = addingText;

        quantity = mainCounter.getQuantity();
        if (groupCounter) {
            groupTicketQuantity = groupCounter.getQuantity();
        }

        fetch('/api/program/add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                eventSessionId: sessionId,
                quantity: quantity,
                groupTicketQuantity: groupTicketQuantity,
                donationAmount: 0
            })
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data.success) {
                    showToast(successText);
                }
                widget.remove();
                originalBtn.style.display = '';
            })
            .catch(function (err) {
                console.error('Failed to add to program:', err);
                widget.remove();
                originalBtn.style.display = '';
            });
    });
}

function initAddPassToCartButtons() {
    document.querySelectorAll('.js-add-pass-to-cart:not([data-atp-init])').forEach(function (btn) {
        btn.setAttribute('data-atp-init', '1');
        btn.addEventListener('click', function () {
            showPassCounterWidget(btn);
        });
    });
}

window.initAddPassToCartButtons = initAddPassToCartButtons;

function showPassCounterWidget(originalBtn) {
    var passTypeId = parseInt(originalBtn.getAttribute('data-pass-type-id'), 10);
    var confirmText = originalBtn.getAttribute('data-confirm-text') || 'Confirm selection';
    var addingText = originalBtn.getAttribute('data-adding-text') || 'Adding...';
    var successText = originalBtn.getAttribute('data-success-text') || 'Added to program';
    var container = originalBtn.parentElement;
    var quantity = 1;

    originalBtn.style.display = 'none';

    var widget = document.createElement('div');
    widget.className = 'flex flex-col items-center gap-2';

    var counterRow = document.createElement('div');
    counterRow.className = 'inline-flex items-center gap-2';

    var decreaseBtn = document.createElement('button');
    decreaseBtn.type = 'button';
    decreaseBtn.className = 'w-7 h-7 bg-slate-800 rounded-[5px] flex items-center justify-center hover:bg-slate-700 transition-colors text-white text-lg font-bold';
    decreaseBtn.textContent = '\u2212';
    decreaseBtn.setAttribute('aria-label', 'Decrease quantity');

    var qtyDisplay = document.createElement('span');
    qtyDisplay.className = 'w-8 h-7 bg-stone-100 rounded flex items-center justify-center text-slate-800 text-sm font-semibold';
    qtyDisplay.textContent = String(quantity);

    var increaseBtn = document.createElement('button');
    increaseBtn.type = 'button';
    increaseBtn.className = 'w-7 h-7 bg-slate-800 rounded-[5px] flex items-center justify-center hover:bg-slate-700 transition-colors text-white text-lg font-bold';
    increaseBtn.textContent = '+';
    increaseBtn.setAttribute('aria-label', 'Increase quantity');

    var cancelBtn = document.createElement('button');
    cancelBtn.type = 'button';
    cancelBtn.className = 'w-7 h-7 rounded-[5px] flex items-center justify-center text-slate-500 hover:text-red-500 transition-colors text-lg font-bold';
    cancelBtn.textContent = '\u00D7';
    cancelBtn.setAttribute('aria-label', 'Cancel selection');

    counterRow.appendChild(decreaseBtn);
    counterRow.appendChild(qtyDisplay);
    counterRow.appendChild(increaseBtn);
    counterRow.appendChild(cancelBtn);

    var confirmBtn = document.createElement('button');
    confirmBtn.type = 'button';
    confirmBtn.className = 'px-3.5 py-2 rounded-[10px] bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition-colors duration-200';
    confirmBtn.textContent = confirmText;

    widget.appendChild(counterRow);
    widget.appendChild(confirmBtn);
    container.appendChild(widget);

    decreaseBtn.addEventListener('click', function () {
        if (quantity > 1) {
            quantity--;
            qtyDisplay.textContent = String(quantity);
        }
    });

    increaseBtn.addEventListener('click', function () {
        quantity++;
        qtyDisplay.textContent = String(quantity);
    });

    cancelBtn.addEventListener('click', function () {
        widget.remove();
        originalBtn.style.display = '';
    });

    confirmBtn.addEventListener('click', function () {
        confirmBtn.disabled = true;
        confirmBtn.textContent = addingText;

        fetch('/api/program/add-pass', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                passTypeId: passTypeId,
                quantity: quantity
            })
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data.success) {
                    showToast(successText);
                }
                widget.remove();
                originalBtn.style.display = '';
            })
            .catch(function (err) {
                console.error('Failed to add pass to program:', err);
                widget.remove();
                originalBtn.style.display = '';
            });
    });
}

function showToast(message) {
    var toast = document.createElement('div');
    toast.className = 'fixed top-4 left-1/2 -translate-x-1/2 z-50 bg-green-600 text-white rounded-xl px-6 py-3 text-sm font-semibold shadow-lg transition-all duration-300';
    toast.style.transform = 'translateX(-50%) translateY(-100%)';
    toast.style.opacity = '0';
    toast.textContent = message;

    document.body.appendChild(toast);

    // Slide in
    requestAnimationFrame(function () {
        toast.style.transform = 'translateX(-50%) translateY(0)';
        toast.style.opacity = '1';
    });

    // Auto-dismiss after 3 seconds
    setTimeout(function () {
        toast.style.transform = 'translateX(-50%) translateY(-100%)';
        toast.style.opacity = '0';
        setTimeout(function () {
            toast.remove();
        }, 300);
    }, 3000);
}
