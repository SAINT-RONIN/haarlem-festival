<?php
/**
 * @var \App\ViewModels\Program\CheckoutSuccessPageViewModel $viewModel
 */
$successMessage = $viewModel->hasSessionData
    ? 'Your payment has been processed successfully. You will receive your tickets shortly in your email.'
    : 'Your payment was received. Your confirmation and ticket email are being prepared now.';
$ticketCaption = $viewModel->hasSessionData
    ? 'Your scannable ticket QR will arrive in your email shortly.'
    : 'Your ticket QR is still being prepared and will be included in the confirmation email.';
$phoneImagePath = '/assets/Icons/Group%202%201.png';
$appStoreIconPath = '/assets/Icons/App%20Icon.png';
$googlePlayIconPath = '/assets/Icons/Google_Play_2022_icon.svg%201.png';
$qrPattern = [
    '111110000011111',
    '100010101010001',
    '101010111010101',
    '100010001010001',
    '111110101011111',
    '000001110100000',
    '101101001011010',
    '011010111000110',
    '110001010110101',
    '001110100011100',
    '111110001010101',
    '100010111000110',
    '101010001011001',
    '100010110100101',
    '111110001011111',
];
/*
 * Rendered inside the shell's <main>; this partial only emits inner content.
 * The checkout-success page sets mainId='checkout-success-main',
 * mainFocusable=true, mainClass='flex-1 w-full bg-sand py-8 sm:py-10 xl:py-12'
 * on its PublicPageLayout to preserve the original attributes.
 */
?>
    <div class="mx-auto w-full max-w-[122rem] px-4 sm:px-8 lg:px-10 xl:px-16">
        <section class="rounded-[1.75rem] border-2 border-green-700 bg-green-50 px-4 py-5 sm:px-6 lg:px-8 lg:py-6"
                 role="status"
                 aria-live="polite">
            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-green-700 text-white lg:h-12 lg:w-12">
                    <i data-lucide="check" class="h-5 w-5 lg:h-6 lg:w-6" aria-hidden="true"></i>
                </div>
                <p class="max-w-6xl text-xl font-bold leading-8 text-green-700 sm:text-2xl lg:text-[2rem] lg:leading-10">
                    <?= htmlspecialchars($successMessage) ?>
                </p>
            </div>
        </section>

        <section class="mt-10 grid gap-10 xl:grid-cols-2 xl:gap-12">
            <div class="flex flex-col gap-8 lg:flex-row lg:items-stretch lg:gap-10">
                <aside class="w-full max-w-[22rem] shrink-0 rounded-[2.5rem] bg-white p-8 shadow-[0_2px_10px_rgba(0,0,0,0.08)] lg:p-10">
                    <div class="grid grid-cols-qr aspect-square gap-1 rounded-[2rem] bg-white p-3 shadow-inner ring-1 ring-slate-200">
                        <?php foreach ($qrPattern as $row): ?>
                            <?php foreach (str_split($row) as $cell): ?>
                                <span class="aspect-square rounded-[3px] <?= $cell === '1' ? 'bg-slate-950' : 'bg-white' ?>"></span>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                    <p class="mt-5 text-center text-sm font-normal leading-5 text-royal-blue">
                        <?= htmlspecialchars($ticketCaption) ?>
                    </p>
                </aside>

                <div class="min-w-0 flex flex-1 flex-col justify-center gap-6 lg:gap-8">
                    <div class="space-y-2">
                        <h2 class="text-2xl font-bold leading-8 text-royal-blue lg:text-[2rem]">More details in your email</h2>
                        <p class="text-xl leading-9 text-slate-700">
                            Check your email for your confirmation and additional event information.
                        </p>
                    </div>

                    <div class="space-y-2">
                        <h3 class="text-2xl font-bold leading-8 text-royal-blue lg:text-[2rem]">How to use your ticket(s)</h3>
                        <p class="text-xl leading-9 text-slate-700">
                            Present the QR code from your email at the venue entrance. Make sure your screen brightness is turned up for quick scanning. Each ticket can be scanned once.
                        </p>
                    </div>

                    <div class="space-y-2">
                        <h3 class="text-2xl font-bold leading-8 text-royal-blue lg:text-[2rem]">Your Tickets</h3>
                        <?php if ($viewModel->orderReference !== null): ?>
                            <p class="text-xl leading-7 text-slate-700">
                                Order number: <?= htmlspecialchars($viewModel->orderReference) ?>
                            </p>
                        <?php else: ?>
                            <p class="text-xl leading-7 text-slate-700">
                                Your order number will be included in the confirmation email.
                            </p>
                        <?php endif; ?>
                        <?php if ($viewModel->totalLabel !== null): ?>
                            <p class="text-lg leading-7 text-slate-600">
                                Total paid: <?= htmlspecialchars($viewModel->totalLabel) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <a href="/"
                       class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-royal-blue px-6 py-4 text-xl font-normal text-white transition-colors duration-200 hover:bg-red focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2 lg:max-w-[26rem]">
                        <i data-lucide="chevron-left" class="h-5 w-5" aria-hidden="true"></i>
                        <span>Back to homepage</span>
                    </a>
                </div>
            </div>

            <div class="flex flex-col gap-8 lg:flex-row lg:items-center lg:gap-8">
                <div class="mx-auto w-full max-w-[14.75rem] shrink-0">
                    <img src="<?= htmlspecialchars($phoneImagePath) ?>"
                         alt="Festival mobile app preview"
                         class="h-auto w-full object-contain">
                </div>

                <div class="min-w-0 flex flex-1 flex-col justify-center gap-8">
                    <div class="space-y-6">
                        <div class="space-y-3">
                            <h2 class="text-4xl font-bold leading-tight text-royal-blue lg:text-[3.25rem] lg:leading-[1.05]">
                                Get The Festival App
                            </h2>
                            <p class="text-2xl font-medium leading-10 text-slate-700">
                                Download our app to keep all your tickets in one place, quickly view them at the entrance, and access important event details anytime, like the schedule, location info, and any updates.
                            </p>
                        </div>

                        <p class="text-2xl italic leading-10 text-slate-600">
                            Have your tickets ready anytime, even without your email.
                        </p>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="flex items-center justify-center gap-5 rounded-2xl bg-royal-blue px-5 py-6 text-white shadow-[0_2px_4px_rgba(0,0,0,0.14)]">
                            <img src="<?= htmlspecialchars($appStoreIconPath) ?>"
                                 alt="App Store"
                                 class="h-12 w-12 rounded-full object-contain">
                            <span class="text-2xl font-medium">App Store</span>
                        </div>

                        <div class="flex items-center justify-center gap-5 rounded-2xl bg-royal-blue px-5 py-6 text-white shadow-[0_2px_4px_rgba(0,0,0,0.14)]">
                            <img src="<?= htmlspecialchars($googlePlayIconPath) ?>"
                                 alt="Google Play"
                                 class="h-12 w-11 object-contain">
                            <span class="text-2xl font-medium">Google Play</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
