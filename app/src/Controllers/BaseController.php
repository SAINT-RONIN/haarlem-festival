<?php

declare(strict_types=1);

namespace App\Controllers;

use App\ViewModels\BaseViewModel;

/**
 * Base controller for pages rendered from a single BaseViewModel instance.
 */
abstract class BaseController
{
    protected function renderPage(string $viewPath, BaseViewModel $viewModel): void
    {
        $globalData = $viewModel->getGlobalData();
        $cms = $globalData['cms'];
        $currentPage = $globalData['currentPage'];
        $includeNav = $globalData['includeNav'];
        $isLoggedIn = $viewModel->globalUi->isLoggedIn;

        require $viewPath;
    }

    protected function renderView(string $viewPath, object $viewModel): void
    {
        require $viewPath;
    }
}
