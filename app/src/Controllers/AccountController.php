<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\Domain\Account\UpdateProfileFormData;
use App\Exceptions\AccountException;
use App\Exceptions\ValidationException;
use App\Services\Interfaces\IAccountService;
use App\Services\Interfaces\IMediaAssetService;
use App\Services\Interfaces\ISessionService;
use App\ViewModels\Account\AccountFormViewModel;


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
                //get user by id
                $user = $this->accountService->getCurrentUser($userId);
            } catch (AccountException $e) {
                $this->redirectAndExit('/login');
            }

            $errors = $this->sessionService->consumeFlash('account_errors') ?? [];
            $successMessage = $this->sessionService->consumeFlash('account_success') ?? '';
            $oldInput = $this->sessionService->consumeFlash('account_input') ?? [];

            $viewModel = new AccountFormViewModel(user: $user, errors: $errors, successMessage: $successMessage, oldInput: $oldInput,);

            require __DIR__ . '/../Views/pages/account.php';
        });
    }

    public function updateProfile(): void
    {
        $this->handlePageRequest(function (): void {
            $userId = $this->requireUserId();

            try {
                $user = $this->accountService->getCurrentUser($userId);
                $data = $this->prepareValidatedProfileData($user->userAccountId);
                $uploadedProfilePictureAssetId = $this->handleProfilePictureUpload();
                $data = $data->withProfilePictureAssetId($uploadedProfilePictureAssetId);

                $this->accountService->updateProfile($data, $user);

                $this->redirectWithSuccess('/account', 'Profile updated successfully.');
            } catch (ValidationException $e) {
                $this->redirectWithErrors('/account', $e->getErrors());
            } catch (AccountException $e) {
                $this->redirectWithErrors('/account', ['general' => $e->getMessage()]);
            }
        });
    }

    public function updatePassword(): void
    {
        $this->handlePageRequest(function (): void {
            $userId = $this->requireUserId();
            $currentPassword = $this->readStringPostParam('currentPassword') ?? '';
            $newPassword = $this->readStringPostParam('newPassword') ?? '';
            $confirmPassword = $this->readStringPostParam('confirmPassword') ?? '';
            try {
                $this->accountService->updatePassword($currentPassword, $newPassword, $confirmPassword, $userId);
                $this->sessionService->setFlash('account_success', 'Password updated successfully.');
                $this->redirectAndExit('/account');
            } catch (ValidationException $e) {
                $this->redirectWithErrors('/account', $e->getErrors());
            } catch (AccountException $e) {
                $this->redirectWithErrors('/account', ['general' => $e->getMessage()]);
            }
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

    /**
     * @throws ValidationException
     */
    private function prepareValidatedProfileData(int $userId): UpdateProfileFormData
    {
        $data = $this->extractProfileUpdateData(null);

        $errors = $this->accountService->validateProfileUpdate($data, $userId);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $data;
    }

    private function extractProfileUpdateData(?int $profilePictureAssetId): UpdateProfileFormData
    {
        return UpdateProfileFormData::fromInput([
            'email' => $this->readStringPostParam('email') ?? '',
            'firstName' => $this->readStringPostParam('firstName') ?? '',
            'lastName' => $this->readStringPostParam('lastName') ?? '',
            'profilePictureAssetId' => $profilePictureAssetId,
        ]);
    }

    private function requireUserId(): int
    {
        $userId = $this->sessionService->getUserId();
        if (!$userId || !$this->sessionService->isLoggedIn()) {
            $this->redirectAndExit('/login');
        }
        return $userId;
    }

    private function redirectWithSuccess(string $url, string $message): void
    {
        $this->sessionService->setFlash('account_success', $message);
        $this->redirectAndExit($url);
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

