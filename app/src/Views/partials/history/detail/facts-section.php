<?php
/**
 * Historical location facts section.
 *
 * @var \App\ViewModels\History\HistoricalLocationViewModel $viewModel
 */

// Extract the dedicated view model for this partial
$locationFacts = $viewModel->locationFacts;
?>

<?php if (!empty($locationFacts->facts)) : ?>
    <section class="w-full px-24 pt-12 pb-24 inline-flex flex-col justify-start items-start gap-12 overflow-hidden">
        <div class="w-full flex flex-col justify-start items-start gap-8">
            <h2 class="w-full text-left text-slate-800 text-5xl font-bold leading-[62px] font-['Montserrat']">
                <?= htmlspecialchars($locationFacts->headingText, ENT_QUOTES, 'UTF-8') ?>
            </h2>

            <div class="w-full flex flex-row justify-between items-stretch gap-8">
                <?php foreach ($locationFacts->facts as $fact) : ?>
                    <article class="flex-1 p-12 bg-slate-800 rounded-[10px] flex justify-start items-center gap-2.5">
                        <p class="flex-1 justify-center text-white text-lg font-normal font-['Montserrat'] leading-8">
                            <?= htmlspecialchars($fact, ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
