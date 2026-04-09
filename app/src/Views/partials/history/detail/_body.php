<?php
/**
 * Historical location detail page body — renders all detail sections sequentially.
 *
 * @var \App\ViewModels\History\HistoricalLocationViewModel $viewModel
 */
?>
<?php \App\View\ViewRenderer::render(__DIR__ . '/hero-section.php', ['viewModel' => $viewModel]); ?>
<?php \App\View\ViewRenderer::render(__DIR__ . '/intro-section.php', ['viewModel' => $viewModel]); ?>
<?php \App\View\ViewRenderer::render(__DIR__ . '/facts-section.php', ['viewModel' => $viewModel]); ?>
<?php \App\View\ViewRenderer::render(__DIR__ . '/significance-section.php', ['viewModel' => $viewModel]); ?>
