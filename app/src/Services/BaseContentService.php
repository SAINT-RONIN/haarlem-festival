<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\GlobalUiConstants;
use App\DTOs\Cms\GlobalUiContent;
use App\Exceptions\PageLoadException;
use App\Repositories\Interfaces\IGlobalContentRepository;

abstract class BaseContentService
{
    public function __construct(
        protected readonly IGlobalContentRepository $globalContentRepo,
    ) {}

    protected function loadGlobalUi(): GlobalUiContent
    {
        return $this->globalContentRepo->findGlobalUiContent(
            GlobalUiConstants::PAGE_SLUG,
            GlobalUiConstants::SECTION_KEY,
        );
    }

    /**
     * @template T
     * @param callable(): T $loader
     * @return T
     */
    protected function guardPageLoad(callable $loader, string $message): mixed
    {
        try {
            return $loader();
        } catch (\Throwable $error) {
            throw new PageLoadException($message, 0, $error);
        }
    }
}
