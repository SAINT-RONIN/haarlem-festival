<?php
/** @var \App\ViewModels\Cms\CmsUserFormViewModel $viewModel */
use App\View\ViewRenderer;
ViewRenderer::render(__DIR__ . '/../../partials/cms/_user-form.php', [
    'viewModel' => $viewModel,
]);
?>
