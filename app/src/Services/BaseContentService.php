<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\GlobalUiConstants;
use App\DTOs\Cms\GlobalUiContent;
use App\Exceptions\PageLoadException;
use App\Repositories\Interfaces\IGlobalContentRepository;

/**
 * Base service for page services that need the shared global-UI CMS section.
 *
 * Provides a shared loadGlobalUi() method so child services don't each inject
 * and call IGlobalContentRepository separately. Replaces the old GlobalUiContentLoader
 * service, eliminating a service-to-service dependency.
 */
abstract class BaseContentService
{
    public function __construct(
        protected readonly IGlobalContentRepository $globalContentRepo,
    ) {}

    /**
     * Loads the shared global-UI content block used by every page's header and footer.
     *
     * The global UI section contains site-wide content like navigation links, footer text,
     * and any CMS-editable elements that appear on every page. Using two constants ensures
     * every child service fetches the same shared block without having to know its slug or key.
     */
    protected function loadGlobalUi(): GlobalUiContent
    {
        return $this->globalContentRepo->findGlobalUiContent(
            GlobalUiConstants::PAGE_SLUG,
            GlobalUiConstants::SECTION_KEY,
        );
    }

    /**
     * Wraps any page-assembly code in a consistent error boundary.
     *
     * Pass a callable that builds and returns the page data. If anything inside throws,
     * the exception is caught and re-thrown as a PageLoadException with a readable message,
     * so controllers don't have to handle raw database errors or unexpected exceptions.
     *
     * @template T
     * @param callable(): T $loader The code that assembles the page — must return a value of type T
     * @param string $message The human-readable message for the PageLoadException if something goes wrong
     * @return T Whatever the loader callable returns
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
