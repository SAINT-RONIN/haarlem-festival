<?php
/**
 * Historical location intro section.
 *
 * @var \App\ViewModels\History\HistoricalLocationViewModel $viewModel
 */

// Extract the dedicated view model for this partial
$intro = $viewModel->locationIntroduction;
?>

<section class="w-full px-24 pt-12 pb-24 inline-flex flex-col justify-start items-start gap-12 overflow-hidden">
    <div class="w-full flex flex-col justify-start items-center gap-5">
        <h2 class="w-full text-left text-slate-800 text-5xl font-bold leading-[62px]">
            <?= htmlspecialchars($intro->headingText, ENT_QUOTES, 'UTF-8') ?>
        </h2>

        <div class="w-full flex flex-row justify-between items-stretch gap-8">
            <div class="w-1/2 flex flex-col justify-between items-start gap-6">
                <div class="w-full flex justify-center items-center">
                    <p class="text-slate-800 text-lg font-normal leading-8">
                        <?= htmlspecialchars($intro->introText, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>

                <div class="w-full p-6 bg-white rounded-2xl flex flex-col justify-center items-center gap-2.5 overflow-hidden">
                    <p class="text-slate-800 text-lg font-normal leading-8">
                        <?= nl2br(htmlspecialchars($intro->factText, ENT_QUOTES, 'UTF-8')) ?>
                    </p>
                </div>
            </div>

            <?php if (!empty($intro->locationImagePath)) : ?>
                <div class="w-1/2 flex items-stretch">
                    <img
                        class="w-full h-full object-cover rounded-2xl"
                        src="<?= htmlspecialchars($intro->locationImagePath, ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars($intro->headingText, ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
