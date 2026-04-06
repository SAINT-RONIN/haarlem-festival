(function () {
    const config = window.ticketScannerConfig || {};
    const scanEndpoint = config.scanEndpoint || '';
    const reader = document.getElementById('scanner-reader');
    const video = document.getElementById('scanner-video');
    const toggleButton = document.getElementById('scanner-toggle');
    const supportNotice = document.getElementById('scanner-support');
    const form = document.getElementById('scanner-form');
    const input = document.getElementById('ticket-code');
    const resultCard = document.getElementById('scanner-result');
    const resultTitle = document.getElementById('scanner-result-title');
    const resultMessage = document.getElementById('scanner-result-message');
    const resultCode = document.getElementById('scanner-result-code');
    const resultTime = document.getElementById('scanner-result-time');
    const scanCanvas = document.createElement('canvas');
    const scanContext = scanCanvas.getContext('2d', { willReadFrequently: true });

    let detector = null;
    let html5QrCode = null;
    let stream = null;
    let scanning = false;
    let usingHtml5Qr = false;
    let lastScannedCode = '';
    let lastScannedAt = 0;
    let consecutiveDetectionErrors = 0;

    function normalizeTicketCode(rawValue) {
        const normalizedValue = (rawValue || '').trim().toUpperCase();
        if (!normalizedValue) {
            return '';
        }

        const match = normalizedValue.match(/HF-[A-Z0-9]+/);
        return match ? match[0] : normalizedValue;
    }

    function setResult(state, title, message, code, scannedAt) {
        resultCard.dataset.state = state;
        resultCard.className = [
            'rounded-[2rem] border p-6 shadow-[0px_20px_50px_rgba(26,42,64,0.10)]',
            state === 'success' ? 'border-green-300 bg-green-50' :
                state === 'error' ? 'border-red-300 bg-red-50' :
                    'border-slate-200 bg-white',
        ].join(' ');
        resultTitle.textContent = title;
        resultMessage.textContent = message;
        resultCode.textContent = code || '-';
        resultTime.textContent = scannedAt || '-';
    }

    function supportsHtml5Qr() {
        return typeof window.Html5Qrcode === 'function';
    }

    function supportsBarcodeDetector() {
        return 'BarcodeDetector' in window;
    }

    function supportsJsQr() {
        return typeof window.jsQR === 'function';
    }

    function showReader() {
        if (reader) {
            reader.classList.remove('hidden');
        }

        if (video) {
            video.classList.add('hidden');
        }
    }

    function showVideo() {
        if (reader) {
            reader.classList.add('hidden');
            reader.innerHTML = '';
        }

        if (video) {
            video.classList.remove('hidden');
        }
    }

    async function ensureDetector() {
        if (!supportsBarcodeDetector()) {
            return null;
        }

        if (detector) {
            return detector;
        }

        const supported = await window.BarcodeDetector.getSupportedFormats();
        if (!supported.includes('qr_code')) {
            return null;
        }

        detector = new window.BarcodeDetector({ formats: ['qr_code'] });
        return detector;
    }

    async function startCamera() {
        if (scanning) {
            await stopCamera();
            return;
        }

        if (supportsHtml5Qr() && reader) {
            const started = await startHtml5QrCamera();
            if (started) {
                return;
            }
        }

        await startFallbackCamera();
    }

    async function startHtml5QrCamera() {
        showReader();

        if (!html5QrCode) {
            html5QrCode = new window.Html5Qrcode('scanner-reader');
        }

        const onScanSuccess = function (decodedText) {
            submitDetectedCode((decodedText || '').trim());
        };

        const onScanFailure = function () {
            // Ignore per-frame misses.
        };

        const scannerConfig = {
            fps: 10,
            qrbox: { width: 260, height: 260 },
            aspectRatio: 4 / 3,
            formatsToSupport: window.Html5QrcodeSupportedFormats
                ? [window.Html5QrcodeSupportedFormats.QR_CODE]
                : undefined,
        };

        const cameraConfigs = [
            { facingMode: 'environment' },
            { facingMode: 'user' },
        ];

        for (const cameraConfig of cameraConfigs) {
            try {
                await html5QrCode.start(cameraConfig, scannerConfig, onScanSuccess, onScanFailure);
                scanning = true;
                usingHtml5Qr = true;
                consecutiveDetectionErrors = 0;
                toggleButton.textContent = 'Stop Camera';
                supportNotice.textContent = 'Camera active. Hold a ticket QR code steady inside the frame.';
                return true;
            } catch (error) {
                // Try the next available camera mode.
            }
        }

        usingHtml5Qr = false;
        if (reader) {
            reader.classList.add('hidden');
            reader.innerHTML = '';
        }
        supportNotice.textContent = 'Advanced QR scanning could not start. Falling back to basic camera mode.';
        return false;
    }

    async function startFallbackCamera() {
        if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
            supportNotice.textContent = 'Camera access is not available in this browser. Use manual ticket entry below.';
            return;
        }

        const qrDetector = await ensureDetector();
        if (!qrDetector && !supportsJsQr()) {
            supportNotice.textContent = 'This browser does not support live QR detection. Use manual ticket entry below.';
            return;
        }

        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'environment',
                    width: { ideal: 1280 },
                    height: { ideal: 720 },
                },
                audio: false,
            });
        } catch (error) {
            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: true,
                    audio: false,
                });
            } catch (fallbackError) {
                supportNotice.textContent = 'Camera access failed. Use manual ticket entry below.';
                return;
            }
        }

        showVideo();
        video.srcObject = stream;
        await waitForVideoFrame();
        scanning = true;
        usingHtml5Qr = false;
        consecutiveDetectionErrors = 0;
        toggleButton.textContent = 'Stop Camera';
        supportNotice.textContent = qrDetector
            ? 'Camera active. Hold a ticket QR code in front of the lens.'
            : 'Camera active. QR decoding fallback is running; hold the ticket steady in good light.';
        requestAnimationFrame(scanFrame);
    }

    async function stopCamera(message) {
        scanning = false;
        toggleButton.textContent = 'Start Camera';
        supportNotice.textContent = message || 'Camera stopped. Start it again or use manual ticket entry below.';

        if (html5QrCode && usingHtml5Qr) {
            try {
                await html5QrCode.stop();
            } catch (error) {
                // Ignore stop failures if the camera is already closed.
            }

            try {
                html5QrCode.clear();
            } catch (error) {
                // Ignore clear failures and reset the container manually.
            }
        }

        if (reader) {
            reader.classList.add('hidden');
            reader.innerHTML = '';
        }

        if (stream) {
            stream.getTracks().forEach((track) => track.stop());
            stream = null;
        }

        if (video) {
            video.srcObject = null;
            video.classList.add('hidden');
        }

        usingHtml5Qr = false;
    }

    async function waitForVideoFrame() {
        try {
            await video.play();
        } catch (error) {
            // Some browsers auto-play the stream without needing an explicit play() call.
        }

        if (video.readyState >= 2 && video.videoWidth > 0 && video.videoHeight > 0) {
            return;
        }

        await new Promise((resolve) => {
            const onLoadedData = function () {
                video.removeEventListener('loadeddata', onLoadedData);
                resolve();
            };

            video.addEventListener('loadeddata', onLoadedData, { once: true });
            setTimeout(resolve, 1000);
        });
    }

    async function scanFrame() {
        if (!scanning || usingHtml5Qr || !video || video.readyState < 2 || !scanContext) {
            if (scanning && !usingHtml5Qr) {
                requestAnimationFrame(scanFrame);
            }
            return;
        }

        if (video.videoWidth === 0 || video.videoHeight === 0) {
            requestAnimationFrame(scanFrame);
            return;
        }

        try {
            scanCanvas.width = video.videoWidth;
            scanCanvas.height = video.videoHeight;
            scanContext.drawImage(video, 0, 0, scanCanvas.width, scanCanvas.height);
            consecutiveDetectionErrors = 0;

            const rawValue = await detectQrCode();
            submitDetectedCode(rawValue);
        } catch (error) {
            consecutiveDetectionErrors += 1;
            if (consecutiveDetectionErrors >= 3) {
                await stopCamera('Live camera scanning failed on this device. Use manual ticket entry below.');
                return;
            }

            supportNotice.textContent = 'Camera is active, but QR detection is retrying. Hold the code steady in good light.';
        }

        requestAnimationFrame(scanFrame);
    }

    async function detectQrCode() {
        if (detector) {
            try {
                const barcodes = await detector.detect(scanCanvas);
                if (barcodes.length > 0) {
                    return (barcodes[0].rawValue || '').trim();
                }
            } catch (error) {
                if (!supportsJsQr()) {
                    throw error;
                }
            }
        }

        if (!supportsJsQr()) {
            return '';
        }

        const imageData = scanContext.getImageData(0, 0, scanCanvas.width, scanCanvas.height);
        const result = window.jsQR(imageData.data, imageData.width, imageData.height, {
            inversionAttempts: 'attemptBoth',
        });

        return result && typeof result.data === 'string' ? result.data.trim() : '';
    }

    function submitDetectedCode(rawValue) {
        const ticketCode = normalizeTicketCode(rawValue);
        const now = Date.now();

        if (!ticketCode || (ticketCode === lastScannedCode && now - lastScannedAt <= 3000)) {
            return;
        }

        lastScannedCode = ticketCode;
        lastScannedAt = now;
        void submitTicketCode(ticketCode);
    }

    async function submitTicketCode(ticketCode) {
        if (!scanEndpoint) {
            setResult('error', 'Scanner unavailable', 'Scan endpoint is not configured.', ticketCode, '');
            return;
        }

        setResult('idle', 'Checking ticket', 'Validating ticket code...', ticketCode, '');

        try {
            const response = await fetch(scanEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ ticketCode }),
            });

            const payload = await response.json();
            if (!response.ok || !payload.success) {
                throw new Error(payload.error || 'Ticket validation failed.');
            }

            input.value = payload.ticketCode || ticketCode;
            setResult('success', 'Entry approved', payload.message || 'Ticket scanned successfully.', payload.ticketCode || ticketCode, payload.scannedAt || '');
        } catch (error) {
            setResult('error', 'Entry rejected', error.message || 'Ticket validation failed.', ticketCode, '');
        }
    }

    toggleButton?.addEventListener('click', function () {
        void startCamera();
    });

    form?.addEventListener('submit', function (event) {
        event.preventDefault();
        void submitTicketCode(normalizeTicketCode(input.value));
    });

    if (supportsHtml5Qr()) {
        supportNotice.textContent = 'Live QR scanning is ready. Start the camera and hold the ticket steady in frame.';
    } else if (!supportsBarcodeDetector() && !supportsJsQr()) {
        supportNotice.textContent = 'Live QR scanning is not supported in this browser. Use manual ticket entry below.';
    } else if (!supportsBarcodeDetector() && supportsJsQr()) {
        supportNotice.textContent = 'Camera scanning is available with fallback QR decoding. Use good lighting for best results.';
    }
})();
