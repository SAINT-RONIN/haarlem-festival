<?php
/**
 * @var string $currentView
 */
?>
        <!-- Page Header -->
        <header class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Ticket Scanner</h1>
            <p class="text-sm text-gray-500 mt-1">Scan QR codes at the venue entrance to validate tickets.</p>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Scanner Column -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">QR Code Scanner</h2>
                </div>
                <div class="p-6">
                    <!-- Camera Scanner -->
                    <div id="qr-reader" class="w-full rounded-lg overflow-hidden mb-4"></div>

                    <div id="scanner-controls" class="flex gap-3">
                        <button type="button" id="btn-start-scanner"
                                class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                            <i data-lucide="camera" class="w-4 h-4" aria-hidden="true"></i>
                            Start Camera
                        </button>
                        <button type="button" id="btn-stop-scanner"
                                class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors hidden flex items-center justify-center gap-2">
                            <i data-lucide="camera-off" class="w-4 h-4" aria-hidden="true"></i>
                            Stop Camera
                        </button>
                    </div>

                    <!-- Divider -->
                    <div class="flex items-center gap-3 my-6">
                        <div class="flex-1 h-px bg-gray-200"></div>
                        <span class="text-sm text-gray-400">or enter code manually</span>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>

                    <!-- Manual Input -->
                    <form id="manual-scan-form" class="flex gap-3">
                        <input type="text" id="manual-ticket-code" placeholder="e.g. HF-ABCDEFGH23456789"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 uppercase"
                               maxlength="30" autocomplete="off">
                        <button type="submit"
                                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition-colors flex items-center gap-2">
                            <i data-lucide="search" class="w-4 h-4" aria-hidden="true"></i>
                            Scan
                        </button>
                    </form>
                </div>
            </div>

            <!-- Result Column -->
            <div>
                <!-- Result Panel (hidden by default) -->
                <div id="scan-result" class="bg-white rounded-lg shadow hidden">
                    <div id="result-header" class="px-6 py-4 border-b border-gray-200">
                        <h2 id="result-title" class="text-lg font-semibold"></h2>
                    </div>
                    <div id="result-body" class="p-6">
                        <!-- Populated by JS -->
                    </div>
                    <div class="px-6 pb-6">
                        <button type="button" id="btn-scan-next"
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                            <i data-lucide="scan-line" class="w-4 h-4" aria-hidden="true"></i>
                            Scan Next Ticket
                        </button>
                    </div>
                </div>

                <!-- Placeholder when no scan result -->
                <div id="scan-placeholder" class="bg-white rounded-lg shadow">
                    <div class="p-12 text-center">
                        <i data-lucide="scan-line" class="w-16 h-16 text-gray-300 mx-auto mb-4" aria-hidden="true"></i>
                        <p class="text-gray-500 text-lg">Waiting for scan...</p>
                        <p class="text-gray-400 text-sm mt-1">Point the camera at a ticket QR code or enter the code manually.</p>
                    </div>
                </div>
            </div>
        </div>
