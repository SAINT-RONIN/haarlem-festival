<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Mappers\CheckoutContentMapper;
use App\DTOs\Cms\CheckoutMainContent;
use App\DTOs\Cms\ProgramMainContent;

class CheckoutContentRepository extends BaseContentRepository implements Interfaces\ICheckoutContentRepository
{
    public function findCheckoutMainContent(string $pageSlug, string $sectionKey): CheckoutMainContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return CheckoutContentMapper::mapCheckout($raw);
    }

    public function findProgramMainContent(string $pageSlug, string $sectionKey): ProgramMainContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return CheckoutContentMapper::mapProgram($raw);
    }
}
