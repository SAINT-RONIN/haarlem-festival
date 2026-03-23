<?php
/**
 * Important tour information section for the History page.
 *
 * Expects a \App\ViewModels\History\HistoryPageViewModel instance as $viewModel
 * and uses its infoAboutTourData property.
 *
 * @var \App\ViewModels\History\HistoryPageViewModel $viewModel
 */

use App\ViewModels\History\ImportantInfoAboutTour;

/** @var ImportantInfoAboutTour $info */
$info = $viewModel->infoAboutTourData;
?>
<section class="self-stretch px-6 lg:px-24 py-12 inline-flex flex-col justify-start items-start gap-6 overflow-hidden">
    <div class="self-stretch flex flex-col justify-start items-start gap-6">
        <div class="inline-flex justify-center items-center gap-2.5">
            <div class="text-slate-800 text-5xl font-bold font-['Montserrat'] leading-[62px]">
                <?= htmlspecialchars($info->headingText) ?>
            </div>
        </div>
        <div class="self-stretch inline-flex justify-start items-start gap-8 lg:gap-24">
            <div class="flex-1">
                <ul class="list-disc pl-5 text-slate-800 text-lg font-normal font-['Montserrat'] leading-8">
                    <?php foreach ($info->infoItems as $item): ?>
                        <li><?= htmlspecialchars($item) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>
