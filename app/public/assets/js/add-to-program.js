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
    var quantity = initialQuantity || 0;

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
    var selectedSessionId = sessionId;
    var container = originalBtn.parentElement;
    var isHistoryEvent = originalBtn.getAttribute('data-is-history-event') === '1';
    var confirmText = originalBtn.getAttribute('data-confirm-text') || 'Confirm selection';
    var addingText = originalBtn.getAttribute('data-adding-text') || 'Adding...';
    var successText = originalBtn.getAttribute('data-success-text') || 'Added to program';
    var quantity = isHistoryEvent ? 0 : 1;

    var groupTicketQuantity = 0;

    // For history events, hide the price display while the widget is visible
    var historyPriceEl = null;
    if (isHistoryEvent) {
        // Look for the price span within the same CTA/price row container
        var priceRow = container.closest('div.w-full.inline-flex.justify-between.items-center');
        if (priceRow) {
            historyPriceEl = priceRow.querySelector('.js-history-price');
            if (historyPriceEl) {
                historyPriceEl.style.visibility = 'hidden';
            }
        }
    }

    function cleanupWidget() {
        if (historyPriceEl) {
            historyPriceEl.style.visibility = '';
        }
        if (widget && widget.parentNode) {
            widget.remove();
        }
        originalBtn.style.display = '';
    }

    originalBtn.style.display = 'none';

    // Main widget wrapper (column layout)
    var widget = document.createElement('div');
    widget.className = 'flex flex-col items-end gap-2';

    // Column of counters (each section stacked vertically)
    var countersWrapper = document.createElement('div');
    countersWrapper.className = 'flex flex-col items-end gap-2';

    // Language selector (history page only, populated after fetching tour info)
    var languageSelectorWrapper = null;
    var languageSelect = null;
    var singleLabelPriceEl = null;
    var groupLabelPriceEl = null;
    var tourInfo = null; // full response from getTourInfo

    if (isHistoryEvent) {
        languageSelectorWrapper = document.createElement('div');
        languageSelectorWrapper.className = 'flex items-center gap-2 self-end mb-1';

        var langLabel = document.createElement('span');
        langLabel.className = 'text-xs text-slate-700 font-semibold';
        langLabel.textContent = 'Language';

        languageSelect = document.createElement('select');
        languageSelect.className = 'border border-gray-300 rounded px-2 py-1 text-xs text-slate-800 bg-white';

        languageSelectorWrapper.appendChild(langLabel);
        languageSelectorWrapper.appendChild(languageSelect);

        widget.appendChild(languageSelectorWrapper);
    }

    var mainCounter = buildTicketCounterRow(quantity);

    // Single tickets section (history only, label + counter in one row)
    var singleRow;
    if (isHistoryEvent) {
        singleRow = document.createElement('div');
        singleRow.className = 'inline-flex items-center gap-2 self-end';

        var singleLabel = document.createElement('span');
        singleLabel.className = 'text-xs text-slate-700 font-semibold';
        singleLabel.textContent = 'Single tickets';

        // price placeholder next to label (updated from tour info)
        singleLabelPriceEl = document.createElement('span');
        singleLabelPriceEl.className = 'text-xs text-slate-700 font-normal';

        singleRow.appendChild(singleLabel);
        singleRow.appendChild(singleLabelPriceEl);
        singleRow.appendChild(mainCounter.row);
        countersWrapper.appendChild(singleRow);
    } else {
        // Non-history: keep original layout (counter row only)
        countersWrapper.appendChild(mainCounter.row);
    }

    // Group tickets section (history only, label + counter in one row)
    var groupCounter = null;
    var groupRow = null;
    if (isHistoryEvent) {
        groupCounter = buildTicketCounterRow(groupTicketQuantity);

        groupRow = document.createElement('div');
        groupRow.className = 'inline-flex items-center gap-2 self-end';

        var groupLabel = document.createElement('span');
        groupLabel.className = 'text-xs text-slate-700 font-semibold';
        groupLabel.textContent = 'Group tickets';

        // price placeholder next to label (updated from tour info)
        groupLabelPriceEl = document.createElement('span');
        groupLabelPriceEl.className = 'text-xs text-slate-700 font-normal';

        groupRow.appendChild(groupLabel);
        groupRow.appendChild(groupLabelPriceEl);
        groupRow.appendChild(groupCounter.row);
        countersWrapper.appendChild(groupRow);
    }

    // Single shared confirm button
    var confirmBtn = document.createElement('button');
    confirmBtn.type = 'button';
    confirmBtn.className = 'px-3.5 py-2 rounded-[10px] bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition-colors duration-200';
    confirmBtn.textContent = confirmText;

    widget.appendChild(countersWrapper);
    widget.appendChild(confirmBtn);
    container.appendChild(widget);

    // Helper to format price (assumes numeric price and currencyCode)
    function formatPrice(priceObj) {
        if (!priceObj) {
            return '';
        }
        var amount = parseFloat(priceObj.price);
        if (isNaN(amount)) {
            return '';
        }

        return amount.toFixed(2) + '€';
    }

    // Update label prices + group counter visibility for a given session info
    function applySessionPricing(infoForSession) {
        if (!isHistoryEvent || !infoForSession) {
            return;
        }

        var prices = infoForSession.prices || [];
        if (prices.length > 0) {
            // Sort by numeric price ascending
            prices.sort(function (a, b) {
                return parseFloat(a.price) - parseFloat(b.price);
            });

            var lowest = prices[0];
            var highest = prices[prices.length - 1];

            if (singleLabelPriceEl) {
                singleLabelPriceEl.textContent = ' (' + formatPrice(lowest) + ')';
            }

            if (groupLabelPriceEl) {
                groupLabelPriceEl.textContent = ' (' + formatPrice(highest) + ')';
            }
        }

        var hasGroupPricing = prices.length > 1;
        var seatsAvailable = infoForSession.seatsAvailable;

        // Hide group counter if there is no second price tier or there are fewer than 4 seats available.
        if (groupRow && (!hasGroupPricing || (seatsAvailable !== null && seatsAvailable < 4))) {
            groupRow.style.display = 'none';
        } else if (groupRow) {
            groupRow.style.display = '';
        }
    }

    function populateHistoryTourOptions(data) {
        if (!data || !data.success || !data.tours) {
            return false;
        }

        tourInfo = data.tours;
        languageSelect.innerHTML = '';

        var firstSessionId = null;
        Object.keys(tourInfo).forEach(function (sessionKey) {
            var info = tourInfo[sessionKey];
            if (!info || !info.language) return;
            var option = document.createElement('option');
            option.value = sessionKey;
            option.textContent = info.language;
            languageSelect.appendChild(option);
            if (firstSessionId === null) {
                firstSessionId = sessionKey;
            }
        });

        if (!languageSelect.hasAttribute('data-history-change-bound')) {
            languageSelect.addEventListener('change', function () {
                var selectedId = languageSelect.value;
                selectedSessionId = parseInt(selectedId, 10) || sessionId;
                applySessionPricing(tourInfo[selectedId]);
            });
            languageSelect.setAttribute('data-history-change-bound', '1');
        }

        if (firstSessionId !== null) {
            languageSelect.value = firstSessionId;
            selectedSessionId = parseInt(firstSessionId, 10) || sessionId;
            applySessionPricing(tourInfo[firstSessionId]);
            return true;
        }

        return false;
    }

    // If this is a history event, fetch tour info to populate dropdown and prices
    if (isHistoryEvent && languageSelect) {
        var preloadedTourInfoRaw = originalBtn.getAttribute('data-history-tour-info');
        var eventId = originalBtn.getAttribute('data-event-id');
        var dateTime = originalBtn.getAttribute('data-datetime');

        if (preloadedTourInfoRaw) {
            try {
                if (populateHistoryTourOptions({
                    success: true,
                    tours: JSON.parse(preloadedTourInfoRaw)
                })) {
                    // History tour info was preloaded in the card HTML, so no extra request is needed.
                    eventId = null;
                    dateTime = null;
                }
            } catch (err) {
                console.error('Failed to parse preloaded tour info:', err);
            }
        }

        if (eventId && dateTime) {
            fetch('/api/program/get-tour-info', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    eventId: parseInt(eventId, 10),
                    dateTime: String(dateTime)
                })
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    populateHistoryTourOptions(data);
                })
                .catch(function (err) {
                    console.error('Failed to load tour info:', err);
                });
        }
    }

    // Single tickets counter wiring
    mainCounter.decreaseBtn.addEventListener('click', function () {
        var current = mainCounter.getQuantity();
        if (current > 1 || (isHistoryEvent && current > 0)) {
            mainCounter.setQuantity(current - 1);
        }
    });

    mainCounter.increaseBtn.addEventListener('click', function () {
        mainCounter.setQuantity(mainCounter.getQuantity() + 1);
    });

    mainCounter.cancelBtn.addEventListener('click', function () {
        if (isHistoryEvent) {
            // For history events, do not close the widget, just reset to 0
            mainCounter.setQuantity(0);
        } else {
            // For non-history events, keep existing behavior: close the widget
            cleanupWidget();
        }
    });

    // Group tickets counter wiring
    if (groupCounter) {
        groupCounter.decreaseBtn.addEventListener('click', function () {
            var current = groupCounter.getQuantity();
            if (current > 1 || (isHistoryEvent && current > 0)) {
                groupCounter.setQuantity(current - 1);
            }
        });

        groupCounter.increaseBtn.addEventListener('click', function () {
            groupCounter.setQuantity(groupCounter.getQuantity() + 1);
        });

        groupCounter.cancelBtn.addEventListener('click', function () {
            // Reset group tickets to 1 but keep widget open
            groupCounter.setQuantity(0);
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

        // For history events, use the session id corresponding to the selected language.
        // For non-history events, this will still be the original sessionId.
        var effectiveSessionId = isHistoryEvent ? selectedSessionId : sessionId;

        if (isHistoryEvent && quantity === 0 && groupTicketQuantity === 0) {
            confirmBtn.disabled = false;
            confirmBtn.textContent = confirmText;
            showToast('Please select at least 1 ticket');
            return;
        }

        fetch('/api/program/add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                eventSessionId: effectiveSessionId,
                quantity: quantity,
                groupTicketQuantity: groupTicketQuantity,
                donationAmount: 0
            })
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    if (!response.ok || !data.success) {
                        throw new Error((data && data.error) || 'Failed to add to program.');
                    }
                    return data;
                });
            })
            .then(function () {
                showToast(successText);
                cleanupWidget();
            })
            .catch(function (err) {
                console.error('Failed to add to program:', err);
                confirmBtn.disabled = false;
                confirmBtn.textContent = confirmText;
                showToast(err.message || 'Failed to add to program.', true);
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
            .then(function (response) {
                return response.json().then(function (data) {
                    if (!response.ok || !data.success) {
                        throw new Error((data && data.error) || 'Failed to add pass to program.');
                    }
                    return data;
                });
            })
            .then(function () {
                showToast(successText);
                widget.remove();
                originalBtn.style.display = '';
            })
            .catch(function (err) {
                console.error('Failed to add pass to program:', err);
                confirmBtn.disabled = false;
                confirmBtn.textContent = confirmText;
                showToast(err.message || 'Failed to add pass to program.', true);
            });
    });
}

function showToast(message, isError) {
    var toast = document.createElement('div');
    toast.className = 'fixed top-4 left-1/2 -translate-x-1/2 z-50 text-white rounded-xl px-6 py-3 text-sm font-semibold shadow-lg transition-all duration-300 ' + (isError ? 'bg-red-600' : 'bg-green-600');
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
