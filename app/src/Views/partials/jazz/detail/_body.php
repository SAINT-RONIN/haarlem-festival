<?php
/**
 * Jazz artist detail page body — renders all detail sections sequentially.
 *
 * @var \App\ViewModels\Jazz\JazzArtistDetailPageViewModel $viewModel
 */
?>
<?php \App\View\ViewRenderer::render(__DIR__ . '/hero-section.php', ['viewModel' => $viewModel]); ?>
<?php \App\View\ViewRenderer::render(__DIR__ . '/overview-section.php', ['viewModel' => $viewModel]); ?>
<?php \App\View\ViewRenderer::render(__DIR__ . '/photo-gallery-section.php', ['viewModel' => $viewModel]); ?>
<?php \App\View\ViewRenderer::render(__DIR__ . '/featured-albums-section.php', ['viewModel' => $viewModel]); ?>
<?php \App\View\ViewRenderer::render(__DIR__ . '/listen-now-section.php', ['viewModel' => $viewModel]); ?>
<?php \App\View\ViewRenderer::render(__DIR__ . '/live-cta-section.php', ['viewModel' => $viewModel]); ?>
<?php \App\View\ViewRenderer::render(__DIR__ . '/performances-section.php', ['viewModel' => $viewModel]); ?>
