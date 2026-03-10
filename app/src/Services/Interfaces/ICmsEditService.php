<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

/**
 * Interface for CMS page editing service.
 */
interface ICmsEditService
{
    /**
     * Gets a page with all its sections and items for editing.
     *
     * @param int $pageId Page ID
     * @return array|null Array with 'page', 'sections' keys or null if not found
     */
    public function getPageForEditing(int $pageId): ?array;

    /**
     * Updates multiple CMS items from form submission.
     *
     * @param int $pageId The page ID for validation
     * @param array $items Array of item updates
     * @return array ['success' => bool, 'errors' => array, 'updatedCount' => int]
     */
    public function updatePageItems(int $pageId, array $items): array;
}
