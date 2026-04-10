<?php
/** @var \App\ViewModels\Cms\CmsArtistFormViewModel $viewModel */
use App\View\ViewRenderer;
ViewRenderer::render(__DIR__ . '/../../partials/cms/_artist-form.php', [
    'viewModel' => $viewModel,
]);
?>
