<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Mappers\CmsRestaurantsMapper;
use App\Services\Interfaces\ICmsRestaurantsService;
use App\Services\Interfaces\ISessionService;

class CmsRestaurantsController
{
    public function __construct(
        private readonly ICmsRestaurantsService $restaurantsService,
        private readonly ISessionService $sessionService,
    ) {}

    public function index(): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $currentView = 'restaurants';
            $search = trim($_GET['search'] ?? '');
            $restaurants = $this->restaurantsService->getRestaurants($search ?: null);
            $viewModel = CmsRestaurantsMapper::toListViewModel(
                $restaurants,
                $search,
                $this->sessionService->consumeFlash('success'),
                $this->sessionService->consumeFlash('error'),
                $this->sessionService->getCsrfToken('cms_restaurant_delete'),
            );
            require __DIR__ . '/../Views/pages/cms/restaurants.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function create(): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $currentView = 'restaurants';
            $viewModel   = $this->buildFormViewModel(null, [], []);
            require __DIR__ . '/../Views/pages/cms/restaurant-create.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function store(): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $this->validateCsrf('cms_restaurant_create', '/cms/restaurants/create');
            $data   = $this->extractFormData();
            $errors = $this->restaurantsService->validateForCreate($data);
            if (!empty($errors)) {
                $this->renderCreateForm($data, $errors);
                return;
            }
            $this->restaurantsService->createRestaurant($data);
            $this->redirectWithFlash('Restaurant created successfully.', 'success', '/cms/restaurants');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function edit(string $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $event = $this->restaurantsService->findById((int) $id);
            if ($event === null) {
                http_response_code(404);
                require __DIR__ . '/../Views/pages/errors/404.php';
                return;
            }
            $currentView = 'restaurants';
            $data = CmsRestaurantsMapper::fromEvent($event);
            $viewModel = $this->buildFormViewModel((int) $id, $data, []);
            require __DIR__ . '/../Views/pages/cms/restaurant-edit.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function update(string $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $this->validateCsrf('cms_restaurant_edit_' . $id, '/cms/restaurants/' . $id . '/edit');
            $data   = $this->extractFormData();
            $errors = $this->restaurantsService->validateForUpdate((int) $id, $data);
            if (!empty($errors)) {
                $this->renderEditForm((int) $id, $data, $errors);
                return;
            }
            $this->restaurantsService->updateRestaurant((int) $id, $data);
            $this->redirectWithFlash('Restaurant updated successfully.', 'success', '/cms/restaurants');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function delete(string $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $this->validateCsrf('cms_restaurant_delete', '/cms/restaurants');
            $this->restaurantsService->deleteRestaurant((int) $id);
            $this->redirectWithFlash('Restaurant deactivated successfully.', 'success', '/cms/restaurants');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    private function validateCsrf(string $scope, string $redirectUrl): void
    {
        if (!$this->sessionService->isValidCsrfToken($scope, $_POST['_csrf'] ?? null)) {
            $this->sessionService->setFlash('error', 'Invalid CSRF token. Please try again.');
            header('Location: ' . $redirectUrl);
            exit;
        }
    }

    private function redirectWithFlash(string $message, string $type, string $url): void
    {
        $this->sessionService->setFlash($type, $message);
        header('Location: ' . $url);
        exit;
    }

    private function extractFormData(): array
    {
        return [
            'Title'               => trim($_POST['title'] ?? ''),
            'Slug'                => trim(strtolower($_POST['slug'] ?? '')),
            'ShortDescription'    => trim($_POST['shortDescription'] ?? ''),
            'LongDescriptionHtml' => $_POST['longDescriptionHtml'] ?? '',
            'FeaturedImageAssetId' => isset($_POST['featuredImageAssetId']) && is_numeric($_POST['featuredImageAssetId'])
                ? (int) $_POST['featuredImageAssetId']
                : null,
            'IsActive' => isset($_POST['isActive']) && $_POST['isActive'] === '1',
        ];
    }

    /** @param array<string, string> $errors */
    private function buildFormViewModel(?int $eventId, array $data, array $errors): \App\ViewModels\Cms\CmsRestaurantFormViewModel
    {
        $scope  = $eventId === null ? 'cms_restaurant_create' : 'cms_restaurant_edit_' . $eventId;
        $action = $eventId === null ? '/cms/restaurants' : '/cms/restaurants/' . $eventId . '/edit';
        $title  = $eventId === null ? 'Create Restaurant' : 'Edit Restaurant';
        return CmsRestaurantsMapper::toFormViewModel($eventId, $data, $this->sessionService->getCsrfToken($scope), $action, $title, $errors);
    }

    /** @param array<string, string> $errors */
    private function renderCreateForm(array $data, array $errors): void
    {
        $currentView = 'restaurants';
        $viewModel   = $this->buildFormViewModel(null, $data, $errors);
        require __DIR__ . '/../Views/pages/cms/restaurant-create.php';
    }

    /** @param array<string, string> $errors */
    private function renderEditForm(int $id, array $data, array $errors): void
    {
        $currentView = 'restaurants';
        $viewModel   = $this->buildFormViewModel($id, $data, $errors);
        require __DIR__ . '/../Views/pages/cms/restaurant-edit.php';
    }
}
