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
        private readonly IAccountService $accountService,
        private readonly IMediaAssetService $mediaAssetService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    /**
     * GET /account
     */
    public function showAccount(): void
    {
        $this->handlePageRequest(function (): void {
            $userId = $this->requireUserId();

            try {
                $user = $this->accountService->getCurrentUser($userId);
            } catch (AccountException $e) {
                $this->redirectAndExit('/login');
                return;
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

            try {
                // Extract form data
                $currentPassword = $this->readStringPostParam('currentPassword') ?? '';
                $newPassword = $this->readStringPostParam('newPassword') ?? '';
                $confirmPassword = $this->readStringPostParam('confirmPassword') ?? '';

                // Process password change
                $this->accountService->updatePassword($currentPassword, $newPassword, $confirmPassword, $userId);

                $this->sessionService->setFlash('account_success', 'Password updated successfully.');
                $this->redirectAndExit('/account');
            } catch (ValidationException $e) {
                // Map error messages to specific field keys
                $errors = $this->mapPasswordErrorsFromException($e);
                $this->redirectWithErrors('/account', $errors);
            } catch (AccountException $e) {
                $this->redirectWithErrors('/account', ['error' => $e->getMessage()]);
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


    /**
     * @return array<string, string>
     */
    private function mapPasswordErrorToField(string $errorMessage): array
    {
        if (strpos($errorMessage, 'Current password') !== false) {
            return ['currentPassword' => $errorMessage];
        } elseif (strpos($errorMessage, 'at least 8 characters') !== false) {
            return ['newPassword' => $errorMessage];
        } elseif (strpos($errorMessage, 'do not match') !== false) {
            return ['confirmPassword' => $errorMessage];
        } elseif (strpos($errorMessage, 'New password is required') !== false) {
            return ['newPassword' => $errorMessage];
        }
        return ['password' => $errorMessage];
    }

    /**
     * @return array<string, string>
     */
    private function mapPasswordErrorsFromException(ValidationException $e): array
    {
        $errors = $e->getErrors();

        // If no errors, return empty
        if (!is_array($errors) || empty($errors)) {
            return ['password' => 'An error occurred'];
        }

        // Check if errors are already keyed (from validatePasswordInputs)
        $firstKey = array_key_first($errors);
        if ($firstKey !== 0) {
            // Keys are not numeric (0, 1, 2...), so they're already field names
            return $errors;
        }

        // Errors are indexed (0, 1, 2...), so we need to map each message to a field
        $mappedErrors = [];
        foreach ($errors as $errorMessage) {
            if (is_string($errorMessage)) {
                // Map this message to the appropriate field
                $fieldError = $this->mapPasswordErrorToField($errorMessage);
                $mappedErrors = array_merge($mappedErrors, $fieldError);
            }
        }

        return !empty($mappedErrors) ? $mappedErrors : ['password' => 'Validation failed'];
    }

    private function requireUserId(): int
    {
        $userId = $this->sessionService->getUserId();
        if (!$userId || !$this->sessionService->isLoggedIn()) {
            $this->redirectAndExit('/login');
            throw new AccountException('Not authenticated');
        }
        return $userId;
    }

    /**
     * @param string $url URL to redirect to
     * @param array<string, string> $errors Errors to flash
     * @param array<string, mixed> $input Old form input to flash (optional)
     */
    private function redirectWithErrors(string $url, array $errors, array $input = []): void
    {
        $this->sessionService->setFlash('account_errors', $errors);
        if (!empty($input)) {
            $this->sessionService->setFlash('account_input', $input);
        }
        $this->redirectAndExit($url);
    }
}

