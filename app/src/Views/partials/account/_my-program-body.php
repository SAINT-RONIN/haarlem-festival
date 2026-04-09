<?php
/**
 * My-Program page body — top-level wrapper that renders the selected-events
 * column and the payment-overview column.
 *
 * @var \App\ViewModels\Program\MyProgramPageViewModel $viewModel
 */

use App\View\ViewRenderer;
?>
<!-- Page Title -->
<div class="px-4 sm:px-8 lg:px-24 pt-8 pb-2">
    <h1 class="text-slate-800 text-2xl sm:text-3xl lg:text-4xl font-bold font-['Montserrat']">
        <?= htmlspecialchars($viewModel->pageTitle) ?>
    </h1>
</div>

<!-- Two-Column Layout -->
<div class="px-4 sm:px-8 lg:px-24 py-6 flex flex-col lg:flex-row gap-6 lg:gap-12 items-start">
    <?php ViewRenderer::render(__DIR__ . '/_my-program-selected-events.php', ['viewModel' => $viewModel]); ?>
    <?php ViewRenderer::render(__DIR__ . '/_my-program-payment-overview.php', ['viewModel' => $viewModel]); ?>
</div>
