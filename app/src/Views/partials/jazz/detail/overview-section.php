<?php
/**
 * Jazz artist overview section with lineup and highlights.
 *
 * @var \App\ViewModels\Jazz\JazzArtistDetailPageViewModel $viewModel
 */
?>

<section class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-10 md:py-12">
    <div class="w-full grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
        <article class="xl:col-span-8 flex flex-col gap-5 sm:gap-6">
            <h2 class="text-royal-blue text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold font-['Montserrat'] leading-tight">
                <?= htmlspecialchars($viewModel->overviewHeading) ?>
            </h2>
            <p class="text-royal-blue/90 text-base sm:text-lg md:text-xl font-normal font-['Montserrat'] leading-relaxed">
                <?= htmlspecialchars($viewModel->overviewLead) ?>
            </p>
            <p class="text-royal-blue/80 text-base sm:text-lg md:text-xl font-normal font-['Montserrat'] leading-relaxed">
                <?= htmlspecialchars($viewModel->overviewBodyPrimary) ?>
            </p>
            <p class="text-royal-blue/80 text-base sm:text-lg md:text-xl font-normal font-['Montserrat'] leading-relaxed">
                <?= htmlspecialchars($viewModel->overviewBodySecondary) ?>
            </p>

            <aside class="p-5 sm:p-6 md:p-8 bg-white rounded-2xl shadow-[0px_4px_10px_-6px_rgba(0,0,0,0.10)] shadow-[0px_10px_25px_-5px_rgba(0,0,0,0.10)] border border-slate-200/60">
                <h3 class="text-royal-blue text-2xl sm:text-3xl font-normal font-['Montserrat'] mb-4 sm:mb-5">
                    <?= htmlspecialchars($viewModel->lineupHeading) ?>
                </h3>
                <ul class="space-y-2">
                    <?php foreach ($viewModel->lineup as $member): ?>
                        <li class="text-royal-blue text-sm sm:text-base md:text-lg font-normal font-['Montserrat'] leading-6">
                            <?= htmlspecialchars($member) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>
        </article>

        <aside class="xl:col-span-4 xl:self-end p-4 sm:p-5 bg-white rounded-[10px] border border-slate-200/60 shadow-[0px_4px_6px_-4px_rgba(0,0,0,0.10)] shadow-lg flex flex-col gap-3 sm:gap-4">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-slate-800 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                     aria-hidden="true" focusable="false">
                    <path d="M12 15v6"></path>
                    <path d="M8 21h8"></path>
                    <path d="M17 4h1a2 2 0 0 1 2 2v1a5 5 0 0 1-5 5h-1V4z"></path>
                    <path d="M7 4H6a2 2 0 0 0-2 2v1a5 5 0 0 0 5 5h1V4z"></path>
                    <path d="M8 4h8v2a4 4 0 0 1-8 0V4z"></path>
                </svg>
                <h3 class="text-slate-800 text-xl sm:text-2xl font-normal font-['Montserrat'] leading-7">
                    <?= htmlspecialchars($viewModel->highlightsHeading) ?>
                </h3>
            </div>

            <ul class="list-disc pl-5 space-y-3 marker:text-slate-700/70">
                <?php foreach ($viewModel->highlights as $highlight): ?>
                    <li class="text-slate-700 text-base sm:text-lg font-normal font-['Montserrat'] leading-6 sm:leading-7">
                        <?= htmlspecialchars($highlight) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>
    </div>
</section>
