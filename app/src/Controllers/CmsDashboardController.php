<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Repositories\CmsRepository;
use App\Services\CmsEditService;
use App\Services\MediaAssetService;
use App\Services\SessionService;
use App\ViewModels\CmsPageEditViewModel;

/**
 * Controller for the CMS Dashboard.
 *
 * Handles the main CMS admin panel views including
 * dashboard overview and pages management.
 */
class CmsDashboardController
{
    private SessionService $sessionService;
    private CmsRepository $cmsRepository;
    private CmsEditService $cmsEditService;
    private MediaAssetService $mediaAssetService;

    public function __construct()
    {
        $this->sessionService = new SessionService();
        $this->cmsRepository = new CmsRepository();
        $this->cmsEditService = new CmsEditService();
        $this->mediaAssetService = new MediaAssetService();
    }

    /**
     * Displays the CMS Dashboard.
     * GET /cms
     */
    public function index(): void
    {
        CmsAuthController::requireAdmin();

        $currentView = 'dashboard';
        $recentPages = $this->getRecentPages();
        $activities = $this->getRecentActivities();
        $userName = $this->getUserDisplayName();

        require __DIR__ . '/../Views/pages/cms/dashboard.php';
    }

    /**
     * Displays the CMS Pages list.
     * GET /cms/pages
     */
    public function pages(): void
    {
        CmsAuthController::requireAdmin();

        $currentView = 'pages';
        $searchQuery = $_GET['search'] ?? '';
        $pages = $this->getAllPages();
        $userName = $this->getUserDisplayName();

        require __DIR__ . '/../Views/pages/cms/dashboard.php';
    }

    /**
     * Gets the current user's display name from session.
     */
    private function getUserDisplayName(): string
    {
        $this->sessionService->start();
        return $_SESSION['user_display_name'] ?? 'Administrator';
    }

    /**
     * Gets recently updated pages for the dashboard.
     */
    private function getRecentPages(): array
    {
        try {
            $cmsPages = $this->cmsRepository->findAllPages();
            $pages = $this->mapPagesToRecentFormat(array_slice($cmsPages, 0, 4));

            return !empty($pages) ? $pages : $this->getDefaultRecentPages();
        } catch (\Exception $e) {
            return $this->getDefaultRecentPages();
        }
    }

    /**
     * Maps CMS pages to recent pages format.
     */
    private function mapPagesToRecentFormat(array $cmsPages): array
    {
        $pages = [];
        foreach ($cmsPages as $page) {
            $pages[] = [
                'title' => $page['Title'],
                'status' => 'Published',
                'time' => $this->formatTimeAgo($page['UpdatedAtUtc'] ?? null),
            ];
        }
        return $pages;
    }

    /**
     * Returns fallback data when database is unavailable.
     */
    private function getDefaultRecentPages(): array
    {
        return [
            ['title' => 'Home', 'status' => 'Published', 'time' => '2h ago'],
            ['title' => 'Jazz', 'status' => 'Published', 'time' => 'yesterday'],
            ['title' => 'Dance', 'status' => 'Published', 'time' => '3d ago'],
            ['title' => 'History', 'status' => 'Draft', 'time' => '6d ago'],
        ];
    }

    /**
     * Gets all pages for the pages list view.
     */
    private function getAllPages(): array
    {
        try {
            $cmsPages = $this->cmsRepository->findAllPages();
            $pages = $this->mapPagesToListFormat($cmsPages);

            return !empty($pages) ? $pages : $this->getDefaultAllPages();
        } catch (\Exception $e) {
            return $this->getDefaultAllPages();
        }
    }

    /**
     * Maps CMS pages to pages list format.
     */
    private function mapPagesToListFormat(array $cmsPages): array
    {
        $pages = [];
        foreach ($cmsPages as $page) {
            $pages[] = [
                'id' => $page['CmsPageId'],
                'title' => $page['Title'],
                'slug' => $page['Slug'],
                'status' => 'Published',
                'updatedAt' => $this->formatTimeAgo($page['UpdatedAtUtc'] ?? null),
            ];
        }
        return $pages;
    }

    /**
     * Returns fallback pages list.
     */
    private function getDefaultAllPages(): array
    {
        return [
            ['id' => 1, 'title' => 'Home', 'slug' => 'home', 'status' => 'Published', 'updatedAt' => '2h ago'],
            ['id' => 2, 'title' => 'Jazz', 'slug' => 'jazz', 'status' => 'Published', 'updatedAt' => 'yesterday'],
            ['id' => 3, 'title' => 'Dance', 'slug' => 'dance', 'status' => 'Published', 'updatedAt' => '3d ago'],
            ['id' => 4, 'title' => 'History', 'slug' => 'history', 'status' => 'Draft', 'updatedAt' => '6d ago'],
            ['id' => 5, 'title' => 'Restaurant', 'slug' => 'restaurant', 'status' => 'Published', 'updatedAt' => '1w ago'],
            ['id' => 6, 'title' => 'Storytelling', 'slug' => 'storytelling', 'status' => 'Published', 'updatedAt' => '1w ago'],
        ];
    }

    /**
     * Gets recent activity feed for the dashboard.
     *
     * @return array Array of activities with icon, text, time, and color
     */
    private function getRecentActivities(): array
    {
        // For now, return static activities
        // This could be enhanced to pull from an activity log table
        return [
            ['icon' => 'edit', 'text' => "You updated 'Home'", 'time' => '2h ago', 'color' => 'blue'],
            ['icon' => 'file-text', 'text' => "Draft saved: 'History'", 'time' => 'yesterday', 'color' => 'amber'],
            ['icon' => 'image', 'text' => 'Media uploaded: header.jpg', 'time' => '3d ago', 'color' => 'purple'],
            ['icon' => 'user', 'text' => "User 'Editor' role updated", 'time' => '1w ago', 'color' => 'green'],
        ];
    }

    /**
     * Formats a timestamp as a human-readable "time ago" string.
     *
     * @param string|null $timestamp The UTC timestamp
     * @return string Human-readable time string
     */
    private function formatTimeAgo(?string $timestamp): string
    {
        if ($timestamp === null) {
            return 'recently';
        }

        try {
            $time = new \DateTime($timestamp, new \DateTimeZone('UTC'));
            $now = new \DateTime('now', new \DateTimeZone('UTC'));
            $diff = $now->diff($time);

            if ($diff->days === 0) {
                if ($diff->h === 0) {
                    return $diff->i . 'm ago';
                }
                return $diff->h . 'h ago';
            } elseif ($diff->days === 1) {
                return 'yesterday';
            } elseif ($diff->days < 7) {
                return $diff->days . 'd ago';
            } elseif ($diff->days < 30) {
                $weeks = floor($diff->days / 7);
                return $weeks . 'w ago';
            } else {
                $months = floor($diff->days / 30);
                return $months . 'mo ago';
            }
        } catch (\Exception $e) {
            return 'recently';
        }
    }

    /**
     * Displays the page edit form.
     * GET /cms/pages/{id}/edit
     */
    public function edit(string $id): void
    {
        CmsAuthController::requireAdmin();

        $pageId = (int)$id;
        $pageData = $this->cmsEditService->getPageForEditing($pageId);

        if (!$pageData) {
            http_response_code(404);
            echo 'Page not found';
            return;
        }

        $viewModel = new CmsPageEditViewModel($pageData);
        $viewData = $viewModel->getViewData();

        $page = $viewData['page'];
        $sections = $viewData['sections'];
        $contentLimits = $viewData['contentLimits'];
        $imageLimits = $viewData['imageLimits'];
        $userName = $this->getUserDisplayName();

        $successMessage = $_SESSION['cms_success'] ?? null;
        $errorMessage = $_SESSION['cms_error'] ?? null;
        unset($_SESSION['cms_success'], $_SESSION['cms_error']);

        require __DIR__ . '/../Views/pages/cms/page-edit.php';
    }

    /**
     * Handles page content update.
     * POST /cms/pages/{id}/edit
     */
    public function update(string $id): void
    {
        CmsAuthController::requireAdmin();

        $pageId = (int)$id;
        $items = $_POST['items'] ?? [];

        if (empty($items)) {
            $_SESSION['cms_error'] = 'No changes submitted';
            header("Location: /cms/pages/{$pageId}/edit");
            exit;
        }

        $formattedItems = [];
        foreach ($items as $itemId => $value) {
            $formattedItems[$itemId] = ['value' => $value];
        }

        $result = $this->cmsEditService->updatePageItems($pageId, $formattedItems);

        if ($result['success']) {
            $_SESSION['cms_success'] = "Updated {$result['updatedCount']} item(s) successfully";
        } else {
            $_SESSION['cms_error'] = implode(', ', $result['errors']);
        }

        header("Location: /cms/pages/{$pageId}/edit");
        exit;
    }

    /**
     * Handles image upload via AJAX.
     * POST /cms/pages/{id}/upload-image
     */
    public function uploadImage(string $id): void
    {
        CmsAuthController::requireAdmin();

        header('Content-Type: application/json');

        $pageId = (int)$id;
        $itemId = (int)($_POST['item_id'] ?? 0);

        if (!$itemId) {
            echo json_encode(['success' => false, 'error' => 'Missing item ID']);
            return;
        }

        if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            echo json_encode(['success' => false, 'error' => 'No file uploaded']);
            return;
        }

        try {
            $mediaAsset = $this->mediaAssetService->uploadImage($_FILES['image'], 'cms');
            $this->cmsEditService->updateItemImage($itemId, $mediaAsset['MediaAssetId']);

            echo json_encode([
                'success' => true,
                'mediaAssetId' => $mediaAsset['MediaAssetId'],
                'filePath' => $mediaAsset['FilePath'],
                'message' => 'Image uploaded successfully'
            ]);
        } catch (ValidationException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Upload failed: ' . $e->getMessage()]);
        }
    }
}
