/**
 * Ticket Scanner — Camera QR scanning and manual input for venue entry validation.
 */
(function () {
    'use strict';

    const qrReaderEl = document.getElementById('qr-reader');
    const btnStart = document.getElementById('btn-start-scanner');
    const btnStop = document.getElementById('btn-stop-scanner');
    const manualForm = document.getElementById('manual-scan-form');
    const manualInput = document.getElementById('manual-ticket-code');
    const resultPanel = document.getElementById('scan-result');
    const resultHeader = document.getElementById('result-header');
    const resultTitle = document.getElementById('result-title');
    const resultBody = document.getElementById('result-body');
    const placeholder = document.getElementById('scan-placeholder');
    const btnScanNext = document.getElementById('btn-scan-next');

    let html5QrCode = null;
    let isScanning = false;

    // ── Camera Scanner ──

    function startScanner() {
        if (isScanning) return;

        html5QrCode = new Html5Qrcode('qr-reader');
        html5QrCode.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            onQrCodeDecoded,
            function () { /* ignore scan failures */ }
        ).then(function () {
            isScanning = true;
            btnStart.classList.add('hidden');
            btnStop.classList.remove('hidden');
        }).catch(function (err) {
            showError('Camera access denied or unavailable. Use manual input instead.');
        });
    }

    function stopScanner() {
        if (!isScanning || !html5QrCode) return;

        html5QrCode.stop().then(function () {
            html5QrCode.clear();
            isScanning = false;
            btnStop.classList.add('hidden');
            btnStart.classList.remove('hidden');
        }).catch(function () {
            isScanning = false;
        });
    }

    function onQrCodeDecoded(decodedText) {
        stopScanner();
        submitScan(decodedText.trim());
    }

    // ── Manual Input ──

    manualForm.addEventListener('submit', function (e) {
        e.preventDefault();
        var code = manualInput.value.trim();
        if (code === '') return;
        submitScan(code);
    });

    // ── API Call ──

    function submitScan(ticketCode) {
        disableInputs(true);

        fetch('/api/scanner/scan', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ticketCode: ticketCode })
        })
        .then(function (res) { return res.json().then(function (data) { return { status: res.status, data: data }; }); })
        .then(function (result) {
            if (result.data.success) {
                showSuccess(result.data);
            } else if (result.data.alreadyScanned) {
                showAlreadyScanned(result.data);
            } else {
                showNotFound(result.data.error || 'Ticket not found.');
            }
        })
        .catch(function () {
            showError('Network error. Please try again.');
        })
        .finally(function () {
            disableInputs(false);
            manualInput.value = '';
        });
    }

    // ── Result Display ──

    function showSuccess(data) {
        resultHeader.className = 'px-6 py-4 border-b border-green-200 bg-green-50';
        resultTitle.className = 'text-lg font-semibold text-green-800';
        resultTitle.textContent = 'Ticket Valid';
        resultBody.innerHTML =
            '<div class="space-y-3">' +
                '<div class="flex items-center gap-2 mb-4">' +
                    '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Entry Granted</span>' +
                '</div>' +
                buildInfoRow('Event', data.eventTitle) +
                buildInfoRow('Venue', data.venueName) +
                buildInfoRow('Date/Time', formatDateTime(data.sessionDateTime, data.durationMinutes)) +
                buildInfoRow('Ticket Code', data.ticketCode) +
                buildInfoRow('Order', data.orderNumber) +
            '</div>';
        showResultPanel();
    }

    function showAlreadyScanned(data) {
        resultHeader.className = 'px-6 py-4 border-b border-amber-200 bg-amber-50';
        resultTitle.className = 'text-lg font-semibold text-amber-800';
        resultTitle.textContent = 'Already Scanned';
        resultBody.innerHTML =
            '<div class="space-y-3">' +
                '<div class="flex items-center gap-2 mb-4">' +
                    '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800">Entry Refused</span>' +
                '</div>' +
                buildInfoRow('Scanned At', data.scannedAt || 'Unknown') +
                buildInfoRow('Event', data.eventTitle || '') +
                buildInfoRow('Venue', data.venueName || '') +
                buildInfoRow('Ticket Code', data.ticketCode || '') +
            '</div>';
        showResultPanel();
    }

    function showNotFound(message) {
        resultHeader.className = 'px-6 py-4 border-b border-red-200 bg-red-50';
        resultTitle.className = 'text-lg font-semibold text-red-800';
        resultTitle.textContent = 'Invalid Ticket';
        resultBody.innerHTML =
            '<div class="flex items-center gap-2">' +
                '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">Not Found</span>' +
            '</div>' +
            '<p class="mt-3 text-gray-600">' + escapeHtml(message) + '</p>';
        showResultPanel();
    }

    function showError(message) {
        resultHeader.className = 'px-6 py-4 border-b border-red-200 bg-red-50';
        resultTitle.className = 'text-lg font-semibold text-red-800';
        resultTitle.textContent = 'Error';
        resultBody.innerHTML = '<p class="text-gray-600">' + escapeHtml(message) + '</p>';
        showResultPanel();
    }

    function showResultPanel() {
        resultPanel.classList.remove('hidden');
        placeholder.classList.add('hidden');
        lucide.createIcons();
    }

    // ── Scan Next ──

    btnScanNext.addEventListener('click', function () {
        resultPanel.classList.add('hidden');
        placeholder.classList.remove('hidden');
        manualInput.focus();
    });

    // ── Helpers ──

    function buildInfoRow(label, value) {
        if (!value) return '';
        return '<div class="flex justify-between py-2 border-b border-gray-100">' +
                    '<span class="text-sm font-medium text-gray-500">' + escapeHtml(label) + '</span>' +
                    '<span class="text-sm text-gray-900 font-medium">' + escapeHtml(value) + '</span>' +
               '</div>';
    }

    function formatDateTime(dateTimeStr, durationMinutes) {
        if (!dateTimeStr) return '';
        try {
            var dt = new Date(dateTimeStr);
            var options = { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' };
            var formatted = dt.toLocaleDateString('en-GB', options);
            if (durationMinutes > 0) {
                var endDt = new Date(dt.getTime() + durationMinutes * 60000);
                formatted += ' - ' + endDt.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
            }
            return formatted;
        } catch (e) {
            return dateTimeStr;
        }
    }

    function disableInputs(disabled) {
        manualInput.disabled = disabled;
        manualForm.querySelector('button[type="submit"]').disabled = disabled;
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ── Button Handlers ──

    btnStart.addEventListener('click', startScanner);
    btnStop.addEventListener('click', stopScanner);
})();
