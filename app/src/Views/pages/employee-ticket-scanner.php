<?php
/**
 * @var \App\ViewModels\Employee\TicketScannerPageViewModel $viewModel
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($viewModel->roleLabel) ?> | Haarlem Festival</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">
    <link rel="icon" href="/assets/Icons/Logo.svg" type="image/svg+xml" sizes="any">
    <link rel="stylesheet" href="/assets/css/tokens.css">
</head>
<body class="min-h-screen bg-sand text-slate-900">
<main class="mx-auto flex min-h-screen w-full max-w-7xl flex-col px-6 py-8 lg:px-10">
    <header class="mb-8 flex flex-col gap-4 rounded-3xl bg-royal-blue px-6 py-5 text-sand shadow-lg lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.24em] text-sand/70">Employee Access</p>
            <h1 class="mt-2 text-3xl font-bold"><?= htmlspecialchars($viewModel->roleLabel) ?></h1>
            <p class="mt-2 max-w-2xl text-sm text-sand/80">
                Scan each visitor ticket once at the entrance. Already scanned or invalid tickets will be rejected immediately.
            </p>
        </div>
        <form method="post" action="<?= htmlspecialchars($viewModel->logoutUrl) ?>">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($viewModel->logoutCsrfToken) ?>">
            <button type="submit"
                    class="inline-flex items-center justify-center rounded-2xl bg-sand px-5 py-3 text-sm font-semibold text-royal-blue transition hover:bg-white">
                Logout
            </button>
        </form>
    </header>

    <section class="grid flex-1 gap-8 xl:grid-cols-[1.1fr_0.9fr]">
        <article class="rounded-[2rem] bg-white p-6 shadow-[0px_20px_50px_rgba(26,42,64,0.10)]">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-royal-blue/60">Live Scanner</p>
                    <h2 class="mt-2 text-2xl font-bold text-royal-blue">Camera Check-In</h2>
                </div>
                <button id="scanner-toggle"
                        type="button"
                        class="inline-flex items-center justify-center rounded-2xl bg-royal-blue px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#243856]">
                    Start Camera
                </button>
            </div>

            <div class="mt-6 overflow-hidden rounded-[2rem] border border-slate-200 bg-slate-950">
                <div id="scanner-reader" class="hidden aspect-[4/3] w-full bg-slate-950"></div>
                <video id="scanner-video" class="hidden aspect-[4/3] w-full object-cover" autoplay muted playsinline></video>
            </div>

            <div id="scanner-support"
                 class="mt-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                Camera scanning is loading. If your device does not support QR detection, use manual entry below.
            </div>
        </article>

        <aside class="flex flex-col gap-6">
            <article class="rounded-[2rem] bg-white p-6 shadow-[0px_20px_50px_rgba(26,42,64,0.10)]">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-royal-blue/60">Manual Fallback</p>
                <h2 class="mt-2 text-2xl font-bold text-royal-blue">Enter Ticket Code</h2>

                <form id="scanner-form" class="mt-6 flex flex-col gap-4">
                    <label for="ticket-code" class="text-sm font-medium text-slate-700">Ticket code</label>
                    <input id="ticket-code"
                           name="ticketCode"
                           type="text"
                           autocomplete="off"
                           spellcheck="false"
                           placeholder="HF-XXXXXXXXXXXX"
                           class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-base font-semibold uppercase tracking-[0.12em] text-slate-900 focus:border-royal-blue focus:outline-none focus:ring-2 focus:ring-royal-blue/20">
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-2xl bg-red px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#b32d3d]">
                        Validate Ticket
                    </button>
                </form>
            </article>

            <article id="scanner-result"
                     class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0px_20px_50px_rgba(26,42,64,0.10)]"
                     data-state="idle">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-royal-blue/60">Scan Result</p>
                <h2 id="scanner-result-title" class="mt-2 text-2xl font-bold text-royal-blue">Waiting for ticket</h2>
                <p id="scanner-result-message" class="mt-4 text-base leading-7 text-slate-600">
                    Start the camera or paste a ticket code to check a visitor in.
                </p>
                <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Ticket Code</dt>
                        <dd id="scanner-result-code" class="mt-2 text-lg font-bold text-slate-900">-</dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Scanned At</dt>
                        <dd id="scanner-result-time" class="mt-2 text-lg font-bold text-slate-900">-</dd>
                    </div>
                </dl>
            </article>
        </aside>
    </section>
</main>

<script>
    window.ticketScannerConfig = <?= json_encode([
        'scanEndpoint' => $viewModel->scanEndpoint,
    ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) ?>;
</script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script src="/assets/js/employee-ticket-scanner.js?v=<?= urlencode($viewModel->scriptVersion) ?>"></script>
</body>
</html>
