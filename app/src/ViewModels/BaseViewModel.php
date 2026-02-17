<?php

declare(strict_types=1);

namespace App\ViewModels;

abstract readonly class BaseViewModel
{
    public function __construct(public GlobalUiData $globalUi)
    {
    }

    /**
     * @return array{globalUi: GlobalUiData}
     */
    public function getGlobalData(): array
    {
        return ['globalUi' => $this->globalUi];
    }
}
