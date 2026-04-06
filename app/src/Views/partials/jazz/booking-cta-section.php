<?php
/**
 * Booking call-to-action section partial for Jazz page.
 *
 * @var \App\ViewModels\Jazz\BookingCallToActionData $bookingCtaData
 */

if (!isset($bookingCtaData) && isset($viewModel) && property_exists($viewModel, 'bookingCtaData')) {
    $bookingCtaData = $viewModel->bookingCtaData;
}

if (!isset($bookingCtaData)) {
    return;
}

$renderBookingIcon = static function (string $icon, bool $isHighlighted): string {
    $strokeClass = $isHighlighted ? 'stroke-slate-900' : 'stroke-white';

    return match ($icon) {
        'phone' => '<svg viewBox="0 0 24 24" aria-hidden="true" class="h-5 w-5 ' . $strokeClass . '" fill="none" stroke-width="1.8"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.86 19.86 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.86 19.86 0 0 1 2.08 4.18 2 2 0 0 1 4.06 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.63 2.62a2 2 0 0 1-.45 2.11L8 9.91a16 16 0 0 0 6.09 6.09l1.46-1.24a2 2 0 0 1 2.11-.45c.84.3 1.72.51 2.62.63A2 2 0 0 1 22 16.92Z"/></svg>',
        'clock' => '<svg viewBox="0 0 24 24" aria-hidden="true" class="h-5 w-5 ' . $strokeClass . '" fill="none" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>',
        'location' => '<img src="/assets/Icons/Location-Icon.svg" alt="" class="h-5 w-5 ' . ($isHighlighted ? 'brightness-0' : 'brightness-0 invert') . '" loading="lazy">',
        default => '',
    };
};
?>

<section class="self-stretch bg-slate-800 shadow-md">
    <div class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-10 sm:py-12 md:py-14 lg:py-16">
        <div class="flex flex-col gap-10">
            <div class="flex max-w-4xl flex-col gap-5">
                <h2 class="text-white text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold font-['Montserrat'] leading-tight">
                    <?= htmlspecialchars($bookingCtaData->headingText) ?>
                </h2>
                <p class="max-w-3xl text-white text-base sm:text-lg font-normal font-['Montserrat'] leading-7">
                    <?= htmlspecialchars($bookingCtaData->descriptionText) ?>
                </p>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <?php foreach ($bookingCtaData->cards as $card): ?>
                    <?php
                    $cardSurfaceClass = $card->isHighlighted
                        ? 'bg-stone-100 text-slate-900 shadow-xl'
                        : 'bg-black/10 text-white shadow-lg';
                    $eyebrowClass = $card->isHighlighted ? 'text-slate-900/70' : 'text-white/70';
                    ?>
                    <article class="min-h-[22rem] rounded-[10px] <?= $cardSurfaceClass ?> p-6 sm:p-7 flex flex-col gap-6">
                        <div class="flex flex-col gap-3">
                            <p class="text-sm sm:text-base font-normal font-['Montserrat'] tracking-[0.18em] uppercase <?= $eyebrowClass ?>">
                                <?= htmlspecialchars($card->eyebrowText) ?>
                            </p>
                            <h3 class="text-2xl sm:text-3xl font-medium font-['Montserrat'] leading-tight">
                                <?= htmlspecialchars($card->titleText) ?>
                            </h3>
                            <p class="text-base sm:text-lg font-normal font-['Montserrat'] leading-7">
                                <?= htmlspecialchars($card->descriptionText) ?>
                            </p>
                        </div>

                        <?php if ($card->rows !== []): ?>
                            <div class="mt-auto flex flex-col gap-4">
                                <?php foreach ($card->rows as $row): ?>
                                    <div class="flex items-start gap-3">
                                        <?php if ($row->icon !== ''): ?>
                                            <div class="mt-1 shrink-0">
                                                <?= $renderBookingIcon($row->icon, $card->isHighlighted) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="flex flex-col gap-1">
                                            <?php foreach ($row->lines as $line): ?>
                                                <p class="text-base sm:text-lg font-normal font-['Montserrat'] leading-6">
                                                    <?= htmlspecialchars($line) ?>
                                                </p>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
