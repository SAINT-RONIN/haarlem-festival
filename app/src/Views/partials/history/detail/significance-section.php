<?php
/**
 * Historical location significance section.
 *
 * @var \App\ViewModels\History\HistoricalLocationViewModel $viewModel
 */

// Extract the dedicated view model for this partial
$locationSignificance = $viewModel->locationSignificance;
?>

<?php if (!empty($locationSignificance->architecturalSignificanceText) || !empty($locationSignificance->historicalSignificanceText)) : ?>
    <section class="w-full px-24 pt-12 pb-24 inline-flex justify-center items-stretch gap-12 overflow-hidden">
        <?php if (!empty($locationSignificance->locationImagePath)) : ?>
            <div class="w-1/2 flex items-stretch">
                <img
                    class="w-full h-full object-cover p-2.5 rounded-2xl"
                    src="<?= htmlspecialchars($locationSignificance->locationImagePath, ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars($locationSignificance->architecturalSignificanceHeadingText, ENT_QUOTES, 'UTF-8') ?>"
                >
            </div>
        <?php endif; ?>

        <div class="flex-1 inline-flex flex-col justify-start items-start gap-12">
            <div class="w-full flex flex-col justify-start items-start gap-6 overflow-hidden">
                <div class="w-full inline-flex justify-center items-center gap-2.5">
                    <h3 class="flex-1 justify-center text-slate-800 text-4xl font-bold font-['Montserrat'] leading-[46px]">
                        <?= htmlspecialchars($locationSignificance->architecturalSignificanceHeadingText, ENT_QUOTES, 'UTF-8') ?>
                    </h3>
                </div>
                <p class="w-full justify-start text-slate-800 text-lg font-normal font-['Montserrat'] leading-8">
                    <?= nl2br(htmlspecialchars($locationSignificance->architecturalSignificanceText, ENT_QUOTES, 'UTF-8')) ?>
                </p>
            </div>

            <div class="w-full flex flex-col justify-start items-start gap-6 overflow-hidden">
                <div class="w-full inline-flex justify-center items-center gap-2.5">
                    <h3 class="flex-1 justify-center text-slate-800 text-4xl font-bold font-['Montserrat'] leading-[46px]">
                        <?= htmlspecialchars($locationSignificance->historicalSignificanceHeadingText, ENT_QUOTES, 'UTF-8') ?>
                    </h3>
                </div>
                <p class="w-full justify-start text-slate-800 text-lg font-normal font-['Montserrat'] leading-8">
                    <?= nl2br(htmlspecialchars($locationSignificance->historicalSignificanceText, ENT_QUOTES, 'UTF-8')) ?>
                </p>
            </div>
        </div>
    </section>
<?php endif; ?>
