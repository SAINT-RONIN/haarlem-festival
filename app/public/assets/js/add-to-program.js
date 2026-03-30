/**
 * Add-to-program button interactions for schedule cards.
 * Transforms the button into a counter widget, then posts to the API on confirm.
 */
document.addEventListener('DOMContentLoaded', function () {
    initAddToProgramButtons();
});

function initAddToProgramButtons() {
    document.querySelectorAll('[data-event-session-id]:not([data-atp-init])').forEach(function (btn) {
        btn.setAttribute('data-atp-init', '1');
        btn.addEventListener('click', function () {
            // Only hide price for history events (they have data-history-languages or data-history-has-group)
            var isHistory = btn.hasAttribute('data-history-languages') || btn.hasAttribute('data-history-has-group');
            if (isHistory) {
                hidePriceDisplayForButton(btn);
            }
            showCounterWidget(btn);
        });
    });
}

function hidePriceDisplayForButton(btn) {
    var container = btn.closest('.w-full.inline-flex.justify-between.items-center');
    if (!container) {
        container = btn.parentElement;
    }
    if (!container) return;

    var priceSpan = container.querySelector('.js-event-price-display');
    if (priceSpan) {
        priceSpan.style.display = 'none';
        btn.setAttribute('data-price-hidden', '1');
    }
}

function restorePriceDisplayForButton(btn) {
    if (!btn || btn.getAttribute('data-price-hidden') !== '1') return;

    var container = btn.closest('.w-full.inline-flex.justify-between.items-center');
    if (!container) {
        container = btn.parentElement;
    }
    if (!container) return;

    var priceSpan = container.querySelector('.js-event-price-display');
    if (priceSpan) {
        priceSpan.style.display = '';
        btn.removeAttribute('data-price-hidden');
    }
}

function showCounterWidget(originalBtn) {
    var sessionId = parseInt(originalBtn.getAttribute('data-event-session-id'), 10);
    var price = originalBtn.getAttribute('data-price');
    var isPayWhatYouLike = originalBtn.getAttribute('data-is-pay-what-you-like') === '1';
    var confirmText = originalBtn.getAttribute('data-confirm-text') || 'Confirm selection';
    var addingText = originalBtn.getAttribute('data-adding-text') || 'Adding...';
    var successText = originalBtn.getAttribute('data-success-text') || 'Added to program';
    var container = originalBtn.parentElement;

    // History-specific options
    var historyLanguagesAttr = originalBtn.getAttribute('data-history-languages');
    var historyHasGroup = originalBtn.getAttribute('data-history-has-group') === '1';
    var isHistory = !!historyLanguagesAttr || historyHasGroup;

    originalBtn.style.display = 'none';

    if (isHistory) {
        renderHistoryWidget({
            container: container,
            originalBtn: originalBtn,
            sessionId: sessionId,
            confirmText: confirmText,
            addingText: addingText,
            successText: successText,
            languagesAttr: historyLanguagesAttr,
            hasGroup: historyHasGroup
        });
        return;
    }

    renderDefaultWidget({
        container: container,
        originalBtn: originalBtn,
        sessionId: sessionId,
        confirmText: confirmText,
        addingText: addingText,
        successText: successText
    });
}

function renderDefaultWidget(options) {
    var container = options.container;
    var originalBtn = options.originalBtn;
    var sessionId = options.sessionId;
    var confirmText = options.confirmText;
    var addingText = options.addingText;
    var successText = options.successText;
    var quantity = 1;

    var widget = document.createElement('div');
    widget.className = 'flex flex-col items-end gap-2';

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
        restorePriceDisplayForButton(originalBtn);
    });

    confirmBtn.addEventListener('click', function () {
        confirmBtn.disabled = true;
        confirmBtn.textContent = addingText;

        fetch('/api/program/add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                eventSessionId: sessionId,
                quantity: quantity,
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
                restorePriceDisplayForButton(originalBtn);
            })
            .catch(function (err) {
                console.error('Failed to add to program:', err);
                widget.remove();
                originalBtn.style.display = '';
                restorePriceDisplayForButton(originalBtn);
            });
    });
}

function renderHistoryWidget(options) {
    var container = options.container;
    var originalBtn = options.originalBtn;
    var sessionId = options.sessionId;
    var confirmText = options.confirmText;
    var addingText = options.addingText;
    var successText = options.successText;
    var languagesAttr = options.languagesAttr;
    var hasGroup = options.hasGroup;

    var singleQty = 1;
    var groupQty = 0;

    var languages = [];
    if (languagesAttr) {
        languages = languagesAttr.split(',').map(function (s) { return s.trim(); }).filter(function (s) { return s.length > 0; });
    }
    if (languages.length === 0) {
        languages = ['english', 'dutch', 'chinese'];
    }

    var widget = document.createElement('div');
    widget.className = 'flex flex-col items-end gap-2';

    // Language dropdown
    var langRow = document.createElement('div');
    langRow.className = 'flex items-center gap-2';

    var langLabel = document.createElement('label');
    langLabel.className = 'text-slate-800 text-sm font-medium';
    langLabel.textContent = 'Language';

    var langSelect = document.createElement('select');
    langSelect.className = 'border border-slate-300 rounded-md px-2 py-1 text-sm';

    languages.forEach(function (code) {
        var option = document.createElement('option');
        option.value = code;
        option.textContent = code.toUpperCase();
        langSelect.appendChild(option);
    });

    langRow.appendChild(langLabel);
    langRow.appendChild(langSelect);
    widget.appendChild(langRow);

    // Single tickets counter
    var singleRow = document.createElement('div');
    singleRow.className = 'flex items-center gap-2';

    var singleLabel = document.createElement('span');
    singleLabel.className = 'text-slate-800 text-sm';
    singleLabel.textContent = 'Single tickets';

    var singleDecrease = document.createElement('button');
    singleDecrease.type = 'button';
    singleDecrease.className = 'w-7 h-7 bg-slate-800 rounded-[5px] flex items-center justify-center hover:bg-slate-700 transition-colors text-white text-lg font-bold';
    singleDecrease.textContent = '\u2212';
    singleDecrease.setAttribute('aria-label', 'Decrease single tickets');

    var singleDisplay = document.createElement('span');
    singleDisplay.className = 'w-8 h-7 bg-stone-100 rounded flex items-center justify-center text-slate-800 text-sm font-semibold';
    singleDisplay.textContent = String(singleQty);

    var singleIncrease = document.createElement('button');
    singleIncrease.type = 'button';
    singleIncrease.className = 'w-7 h-7 bg-slate-800 rounded-[5px] flex items-center justify-center hover:bg-slate-700 transition-colors text-white text-lg font-bold';
    singleIncrease.textContent = '+';
    singleIncrease.setAttribute('aria-label', 'Increase single tickets');

    singleRow.appendChild(singleLabel);
    singleRow.appendChild(singleDecrease);
    singleRow.appendChild(singleDisplay);
    singleRow.appendChild(singleIncrease);
    widget.appendChild(singleRow);

    // Group tickets counter (optional)
    var groupRow = null;
    var groupDecrease, groupDisplay, groupIncrease;
    if (hasGroup) {
        groupRow = document.createElement('div');
        groupRow.className = 'flex items-center gap-2';

        var groupLabel = document.createElement('span');
        groupLabel.className = 'text-slate-800 text-sm';
        groupLabel.textContent = 'Group tickets';

        groupDecrease = document.createElement('button');
        groupDecrease.type = 'button';
        groupDecrease.className = 'w-7 h-7 bg-slate-800 rounded-[5px] flex items-center justify-center hover:bg-slate-700 transition-colors text-white text-lg font-bold';
        groupDecrease.textContent = '\u2212';
        groupDecrease.setAttribute('aria-label', 'Decrease group tickets');

        groupDisplay = document.createElement('span');
        groupDisplay.className = 'w-8 h-7 bg-stone-100 rounded flex items-center justify-center text-slate-800 text-sm font-semibold';
        groupDisplay.textContent = String(groupQty);

        groupIncrease = document.createElement('button');
        groupIncrease.type = 'button';
        groupIncrease.className = 'w-7 h-7 bg-slate-800 rounded-[5px] flex items-center justify-center hover:bg-slate-700 transition-colors text-white text-lg font-bold';
        groupIncrease.textContent = '+';
        groupIncrease.setAttribute('aria-label', 'Increase group tickets');

        groupRow.appendChild(groupLabel);
        groupRow.appendChild(groupDecrease);
        groupRow.appendChild(groupDisplay);
        groupRow.appendChild(groupIncrease);
        widget.appendChild(groupRow);
    }

    // Bottom row: cancel + confirm
    var bottomRow = document.createElement('div');
    bottomRow.className = 'flex items-center gap-2 justify-end w-full';

    var cancelBtn = document.createElement('button');
    cancelBtn.type = 'button';
    cancelBtn.className = 'w-7 h-7 rounded-[5px] flex items-center justify-center text-slate-500 hover:text-red-500 transition-colors text-lg font-bold';
    cancelBtn.textContent = '\u00D7';
    cancelBtn.setAttribute('aria-label', 'Cancel selection');

    var confirmBtn = document.createElement('button');
    confirmBtn.type = 'button';
    confirmBtn.className = 'px-3.5 py-2 rounded-[10px] bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition-colors duration-200';
    confirmBtn.textContent = confirmText;

    bottomRow.appendChild(cancelBtn);
    bottomRow.appendChild(confirmBtn);

    widget.appendChild(bottomRow);
    container.appendChild(widget);

    // Interactions
    singleDecrease.addEventListener('click', function () {
        if (singleQty > 0) {
            singleQty--;
            singleDisplay.textContent = String(singleQty);
        }
    });

    singleIncrease.addEventListener('click', function () {
        singleQty++;
        singleDisplay.textContent = String(singleQty);
    });

    if (hasGroup && groupDecrease && groupIncrease && groupDisplay) {
        groupDecrease.addEventListener('click', function () {
            if (groupQty > 0) {
                groupQty--;
                groupDisplay.textContent = String(groupQty);
            }
        });

        groupIncrease.addEventListener('click', function () {
            groupQty++;
            groupDisplay.textContent = String(groupQty);
        });
    }

    cancelBtn.addEventListener('click', function () {
        widget.remove();
        originalBtn.style.display = '';
        restorePriceDisplayForButton(originalBtn);
    });

    confirmBtn.addEventListener('click', function () {
        confirmBtn.disabled = true;
        confirmBtn.textContent = addingText;

        var payload = {
            eventSessionId: sessionId,
            quantity: singleQty + (groupQty || 0),
            donationAmount: 0,
            historyLanguage: langSelect.value,
            historySingleTickets: singleQty,
            historyGroupTickets: groupQty
        };

        fetch('/api/program/add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data.success) {
                    showToast(successText);
                }
                widget.remove();
                originalBtn.style.display = '';
                restorePriceDisplayForButton(originalBtn);
            })
            .catch(function (err) {
                console.error('Failed to add to program:', err);
                widget.remove();
                originalBtn.style.display = '';
                restorePriceDisplayForButton(originalBtn);
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
