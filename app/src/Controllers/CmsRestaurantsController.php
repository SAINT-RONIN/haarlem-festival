<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Mappers\CmsRestaurantsMapper;
use App\Models\RestaurantUpsertData;
use App\Services\Interfaces\ICmsRestaurantsService;
use App\Services\Interfaces\ISessionService;
use App\ViewModels\Cms\CmsRestaurantFormViewModel;

class CmsRestaurantsController extends CmsBaseController
{
    public function __construct(
        private readonly ICmsRestaurantsService $restaurantsService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    public function index(): void
    {
        try {
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
            $currentView = 'restaurants';
            $emptyData   = new RestaurantUpsertData('', '', '', null, '', '', null, true, null, null, null, null, null, null, null, null, null, null, null, null, null);
            $viewModel   = $this->buildFormViewModel(null, $emptyData, []);
            require __DIR__ . '/../Views/pages/cms/restaurant-create.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function store(): void
    {
        try {
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

    public function edit(int $id): void
    {
        try {
            $restaurant = $this->restaurantsService->findById($id);
            if ($restaurant === null) {
                http_response_code(404);
                require __DIR__ . '/../Views/pages/errors/404.php';
                return;
            }
            $currentView = 'restaurants';
            $data = CmsRestaurantsMapper::fromRestaurant($restaurant);
            $viewModel = $this->buildFormViewModel($id, $data, []);
            require __DIR__ . '/../Views/pages/cms/restaurant-edit.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function update(int $id): void
    {
        try {
            $this->validateCsrf('cms_restaurant_edit_' . $id, '/cms/restaurants/' . $id . '/edit');
            $data   = $this->extractFormData();
            $errors = $this->restaurantsService->validateForUpdate($id, $data);
            if (!empty($errors)) {
                $this->renderEditForm($id, $data, $errors);
                return;
            }
            $this->restaurantsService->updateRestaurant($id, $data);
            $this->redirectWithFlash('Restaurant updated successfully.', 'success', '/cms/restaurants');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function delete(int $id): void
    {
        try {
            $this->validateCsrf('cms_restaurant_delete', '/cms/restaurants');
            $this->restaurantsService->deleteRestaurant($id);
            $this->redirectWithFlash('Restaurant deactivated successfully.', 'success', '/cms/restaurants');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    private function extractFormData(): RestaurantUpsertData
    {
        return new RestaurantUpsertData(...[
            ...$this->extractCorePostData(),
            ...$this->extractContactPostData(),
            ...$this->extractDetailPostData(),
        ]);
    }

    /** @return array<string, mixed> */
    private function extractCorePostData(): array
    {
        return [
            'name'            => trim($_POST['name'] ?? ''),
            'addressLine'     => trim($_POST['addressLine'] ?? ''),
            'city'            => trim($_POST['city'] ?? ''),
            'stars'           => isset($_POST['stars']) && is_numeric($_POST['stars']) ? (int) $_POST['stars'] : null,
            'cuisineType'     => trim($_POST['cuisineType'] ?? ''),
            'descriptionHtml' => $_POST['descriptionHtml'] ?? '',
            'imageAssetId'    => isset($_POST['imageAssetId']) && is_numeric($_POST['imageAssetId']) ? (int) $_POST['imageAssetId'] : null,
            'isActive'        => isset($_POST['isActive']) && $_POST['isActive'] === '1',
        ];
    }

    /** @return array<string, mixed> */
    private function extractContactPostData(): array
    {
        return [
            'phone'   => trim($_POST['phone'] ?? '') ?: null,
            'email'   => trim($_POST['email'] ?? '') ?: null,
            'website' => trim($_POST['website'] ?? '') ?: null,
        ];
    }

    /** @return array<string, mixed> */
    private function extractDetailPostData(): array
    {
        return [
            ...$this->extractAboutPostData(),
            ...$this->extractVenuePostData(),
        ];
    }

    /** @return array<string, mixed> */
    private function extractAboutPostData(): array
    {
        return [
            'aboutText'           => $_POST['aboutText'] ?? null ?: null,
            'chefName'            => trim($_POST['chefName'] ?? '') ?: null,
            'chefText'            => $_POST['chefText'] ?? null ?: null,
            'menuDescription'     => $_POST['menuDescription'] ?? null ?: null,
            'locationDescription' => $_POST['locationDescription'] ?? null ?: null,
        ];
    }

    /** @return array<string, mixed> */
    private function extractVenuePostData(): array
    {
        return [
            'mapEmbedUrl'         => trim($_POST['mapEmbedUrl'] ?? '') ?: null,
            'michelinStars'       => isset($_POST['michelinStars']) && is_numeric($_POST['michelinStars']) ? (int)$_POST['michelinStars'] : null,
            'seatsPerSession'     => isset($_POST['seatsPerSession']) && is_numeric($_POST['seatsPerSession']) ? (int)$_POST['seatsPerSession'] : null,
            'durationMinutes'     => isset($_POST['durationMinutes']) && is_numeric($_POST['durationMinutes']) ? (int)$_POST['durationMinutes'] : null,
            'specialRequestsNote' => trim($_POST['specialRequestsNote'] ?? '') ?: null,
        ];
    }

    /** @param array<string, string> $errors */
    private function buildFormViewModel(?int $restaurantId, RestaurantUpsertData $data, array $errors): CmsRestaurantFormViewModel
    {
        $scope  = $restaurantId === null ? 'cms_restaurant_create' : 'cms_restaurant_edit_' . $restaurantId;
        $action = $restaurantId === null ? '/cms/restaurants' : '/cms/restaurants/' . $restaurantId . '/edit';
        $title  = $restaurantId === null ? 'Create Restaurant' : 'Edit Restaurant';
        return CmsRestaurantsMapper::toFormViewModel($restaurantId, $data, $this->sessionService->getCsrfToken($scope), $action, $title, $errors);
    }

    /** @param array<string, string> $errors */
    private function renderCreateForm(RestaurantUpsertData $data, array $errors): void
    {
        $currentView = 'restaurants';
        $viewModel   = $this->buildFormViewModel(null, $data, $errors);
        require __DIR__ . '/../Views/pages/cms/restaurant-create.php';
    }

    /** @param array<string, string> $errors */
    private function renderEditForm(int $id, RestaurantUpsertData $data, array $errors): void
    {
        $currentView = 'restaurants';
        $viewModel   = $this->buildFormViewModel($id, $data, $errors);
        require __DIR__ . '/../Views/pages/cms/restaurant-edit.php';
    }
}
