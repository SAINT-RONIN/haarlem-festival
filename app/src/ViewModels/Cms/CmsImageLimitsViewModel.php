<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

final readonly class CmsImageLimitsViewModel
{
    /**
     * @param string[] $allowedMimes
     * @param string[] $allowedExtensions
     */
    public function __construct(
        public int    $maxWidth,
        public int    $maxHeight,
        public int    $maxFileSize,
        public string $maxFileSizeFormatted,
        public array  $allowedMimes,
        public array  $allowedExtensions,
    ) {}
}
