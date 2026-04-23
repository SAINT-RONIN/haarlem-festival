<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\Domain\Account\UpdateProfileFormData;
use App\Exceptions\AccountException;
use App\Exceptions\ValidationException;
use App\Mappers\AccountMapper;
use App\Services\Interfaces\IAccountService;
use App\Services\Interfaces\IMediaAssetService;
use App\Services\Interfaces\ISessionService;
use App\ViewModels\Account\AccountFormViewModel;

/**
 * Manages authenticated user profile editing and password changes
 */
class AccountController extends BaseController
{
    public function __construct(
        private IAccountService $accountService,
        private IMediaAssetService $mediaAssetService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    public function showAccount(): void
    {
        $this->handlePageRequest(function (): void {
            $userId = $this->requireUserId();

            try {
                $user = $this->accountService->getCurrentUser($userId);
            } catch (AccountException $e) {
                $this->redirectAndExit('/login');
            }

            $errors = $this->sessionService->consumeFlash('account_errors') ?? [];
            $successMessage = $this->sessionService->consumeFlash('account_success') ?? '';
            $oldInput = $this->sessionService->consumeFlash('account_input') ?? [];

            $viewModel = new AccountFormViewModel(
                user: $user,
                errors: $errors,
                successMessage: $successMessage,
                oldInput: $oldInput,
            );

            require __DIR__ . '/../Views/pages/account.php';
        });
    }

    /**
     * POST /account/update-profile
     */
    public function updateProfile(): void
    {
        $this->handlePageRequest(function (): void {
            $userId = $this->requireUserId();
            $data = null;

            try {
                // Extract and handle profile picture
                $profilePictureAssetId = $this->handleProfilePictureUpload();

                // Extract form data
                $data = $this->extractProfileUpdateData($profilePictureAssetId);

                // Validate and process
                $this->processProfileUpdate($data, $userId);

                $this->sessionService->setFlash('account_success', 'Profile updated successfully.');
                $this->redirectAndExit('/account');
            } catch (ValidationException $e) {
                $this->redirectWithErrors('/account', $e->getErrors(), $data->toArray() ?? []);
            } catch (AccountException $e) {
                $this->redirectWithErrors('/account', ['error' => $e->getMessage()], $data->toArray() ?? []);
            }
        });
    }

    /**
     * POST /account/update-password
     */
    public function updatePassword(): void
    {
        $this->handlePageRequest(function (): void {
            $userId = $this->requireUserId();
            $currentPassword = $this->readStringPostParam('currentPassword') ?? '';
            $newPassword = $this->readStringPostParam('newPassword') ?? '';
            $confirmPassword = $this->readStringPostParam('confirmPassword') ?? '';

            $result = $this->accountService->updatePassword($currentPassword, $newPassword, $confirmPassword, $userId);

            if (!$result['success']) {
                $this->redirectWithErrors('/account', $result['errors'], [
                    'currentPassword' => $currentPassword,
                    'newPassword' => $newPassword,
                    'confirmPassword' => $confirmPassword,
                ]);
            }
            $this->sessionService->setFlash('account_success', 'Password updated successfully.');
            $this->redirectAndExit('/account');
        });
    }


    private function handleProfilePictureUpload(): ?int
    {
        if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadedAsset = $this->mediaAssetService->uploadImage($_FILES['profilePicture'], 'profile-pictures');
            return $uploadedAsset->mediaAssetId;
        }
        return null;
    }


    private function extractProfileUpdateData(?int $profilePictureAssetId): UpdateProfileFormData
    {
        return AccountMapper::fromProfileUpdateInput([
            'email' => $this->readStringPostParam('email') ?? '',
            'firstName' => $this->readStringPostParam('firstName') ?? '',
            'lastName' => $this->readStringPostParam('lastName') ?? '',
            'newPassword' => $_POST['newPassword'] ?? '',
            'confirmPassword' => $_POST['confirmPassword'] ?? '',
            'profilePictureAssetId' => $profilePictureAssetId,
        ]);
    }

    private function processProfileUpdate(UpdateProfileFormData $data, int $userId): void
    {
        $errors = $this->accountService->validateProfileUpdate($data, $userId);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $this->accountService->updateProfile($data, $userId);
    }

    private function requireUserId(): int
    {
        $userId = $this->sessionService->getUserId();
        if (!$userId || !$this->sessionService->isLoggedIn()) {
            $this->redirectAndExit('/login');
        }
        return $userId;
    }

    private function redirectWithErrors(string $url, array $errors, array $input = []): void
    {
        $this->sessionService->setFlash('account_errors', $errors);
        if (!empty($input)) {
            $this->sessionService->setFlash('account_input', $input);
        }
        $this->redirectAndExit($url);
    }
}

