<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\CmsRepository;
use App\Services\SessionService;

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

    public function __construct()
    {
        $this->sessionService = new SessionService();
        $this->cmsRepository = new CmsRepository();
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

        require __DIR__ . '/../Views/pages/cms/dashboard.php';
    }

    /**
     * Gets recently updated pages for the dashboard.
     *
     * @return array Array of recent pages with title, status, and time
     */
    private function getRecentPages(): array
    {
        // Fetch actual CMS pages from database
        try {
            $cmsPages = $this->cmsRepository->findAllPages();
            $pages = [];

            foreach (array_slice($cmsPages, 0, 4) as $page) {
                $pages[] = [
                    'title' => $page['Title'],
                    'status' => 'Published', // All CMS pages are published for now
                    'time' => $this->formatTimeAgo($page['UpdatedAtUtc'] ?? null),
                ];
            }

            // If no pages in database, return default - WE WILL REMOVE THIS ONCE WE HAVE REAL DATA
            if (empty($pages)) {
                return [
                    ['title' => 'Home', 'status' => 'Published', 'time' => '2h ago'],
                    ['title' => 'Jazz', 'status' => 'Published', 'time' => 'yesterday'],
                    ['title' => 'Dance', 'status' => 'Published', 'time' => '3d ago'],
                    ['title' => 'History', 'status' => 'Draft', 'time' => '6d ago'],
                ];
            }

            return $pages;
        } catch (\Exception $e) {
            // Fallback to static data if database error
            return [
                ['title' => 'Home', 'status' => 'Published', 'time' => '2h ago'],
                ['title' => 'Jazz', 'status' => 'Published', 'time' => 'yesterday'],
                ['title' => 'Dance', 'status' => 'Published', 'time' => '3d ago'],
                ['title' => 'History', 'status' => 'Draft', 'time' => '6d ago'],
            ];
        }
    }

    /**
     * Gets all pages for the pages list view.
     *
     * @return array Array of pages with id, title, slug, status, updatedAt
     */
    private function getAllPages(): array
    {
        try {
            $cmsPages = $this->cmsRepository->findAllPages();
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

            // If no pages, return default list
            if (empty($pages)) {
                return [
                    ['id' => 1, 'title' => 'Home', 'slug' => 'home', 'status' => 'Published', 'updatedAt' => '2h ago'],
                    ['id' => 2, 'title' => 'Jazz', 'slug' => 'jazz', 'status' => 'Published', 'updatedAt' => 'yesterday'],
                    ['id' => 3, 'title' => 'Dance', 'slug' => 'dance', 'status' => 'Published', 'updatedAt' => '3d ago'],
                    ['id' => 4, 'title' => 'History', 'slug' => 'history', 'status' => 'Draft', 'updatedAt' => '6d ago'],
                    ['id' => 5, 'title' => 'Restaurant', 'slug' => 'restaurant', 'status' => 'Published', 'updatedAt' => '1w ago'],
                    ['id' => 6, 'title' => 'Storytelling', 'slug' => 'storytelling', 'status' => 'Published', 'updatedAt' => '1w ago'],
                ];
            }

            return $pages;
        } catch (\Exception $e) {
            // Fallback to static data
            return [
                ['id' => 1, 'title' => 'Home', 'slug' => 'home', 'status' => 'Published', 'updatedAt' => '2h ago'],
                ['id' => 2, 'title' => 'Jazz', 'slug' => 'jazz', 'status' => 'Published', 'updatedAt' => 'yesterday'],
                ['id' => 3, 'title' => 'Dance', 'slug' => 'dance', 'status' => 'Published', 'updatedAt' => '3d ago'],
                ['id' => 4, 'title' => 'History', 'slug' => 'history', 'status' => 'Draft', 'updatedAt' => '6d ago'],
                ['id' => 5, 'title' => 'Restaurant', 'slug' => 'restaurant', 'status' => 'Published', 'updatedAt' => '1w ago'],
                ['id' => 6, 'title' => 'Storytelling', 'slug' => 'storytelling', 'status' => 'Published', 'updatedAt' => '1w ago'],
            ];
        }
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
}
