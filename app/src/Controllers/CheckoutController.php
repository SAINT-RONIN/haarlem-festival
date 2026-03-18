<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Http\Requests\Interfaces\IStripeWebhookRequestFactory;
use App\Mappers\ProgramMapper;
use App\Services\Interfaces\ICheckoutService;
use App\Services\Interfaces\ICmsPageContentService;
use App\Services\Interfaces\IProgramService;
use App\Services\Interfaces\ISessionService;
use App\ViewModels\Program\CheckoutCancelPageViewModel;
use App\ViewModels\Program\CheckoutSuccessPageViewModel;

class CheckoutController extends BaseController
{
    private IProgramService $programService;
    private ICmsPageContentService $cmsService;
    private ISessionService $sessionService;
    private ICheckoutService $checkoutService;
    private IStripeWebhookRequestFactory $stripeWebhookRequestFactory;

    public function __construct(
        IProgramService $programService,
        ICmsPageContentService $cmsService,
        ISessionService $sessionService,
        ICheckoutService $checkoutService,
        IStripeWebhookRequestFactory $stripeWebhookRequestFactory,
    ) {
        $this->programService = $programService;
        $this->cmsService = $cmsService;
        $this->sessionService = $sessionService;
        $this->checkoutService = $checkoutService;
        $this->stripeWebhookRequestFactory = $stripeWebhookRequestFactory;
    }

    public function index(): void
    {
        try {
            $sessionKey = $this->getSessionKey();
            $userId = $this->getLoggedInUserId();
            $isLoggedIn = $userId !== null;

            $programData = $this->programService->getProgramData($sessionKey, $userId);

            if ($programData['items'] === []) {
                $this->redirect('/my-program');
                return;
            }

            $cmsContent = $this->cmsService->getSectionContent('checkout', 'main');
            $viewModel = ProgramMapper::toCheckoutViewModel($programData, $cmsContent, $isLoggedIn);

            $this->renderView(__DIR__ . '/../Views/pages/checkout.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function createSession(): void
    {
        try {
            $sessionKey = $this->getSessionKey();
            $userId = $this->requireAuthenticatedUserId();

            $payload = $this->readJsonBody();
            $programData = $this->programService->getProgramData($sessionKey, $userId);
            $result = $this->checkoutService->createCheckoutSession($programData, $userId, $payload);

            $this->json([
                'success' => true,
                'redirectUrl' => $result['redirectUrl'],
            ], 200);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respondJson($error, 400);
        }
    }

    public function success(): void
    {
        try {
            $sessionId = $this->readStringQueryParam('session_id', 255);
            $sessionSummary = $sessionId !== null ? $this->checkoutService->getSessionSummary($sessionId) : null;
            $viewModel = new CheckoutSuccessPageViewModel(
                $sessionSummary,
                $this->getLoggedInUserId() !== null,
            );

            $this->renderView(__DIR__ . '/../Views/pages/checkout-success.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function cancel(): void
    {
        try {
            $orderId = $this->readPositiveIntQueryParam('order_id');
            $paymentId = $this->readPositiveIntQueryParam('payment_id');

            $cancelResult = $this->checkoutService->handleCancel($orderId, $paymentId);
            $viewModel = new CheckoutCancelPageViewModel(
                $cancelResult,
                $this->getLoggedInUserId() !== null,
            );

            $this->renderView(__DIR__ . '/../Views/pages/checkout-cancel.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function webhook(): void
    {
        try {
            $webhookRequest = $this->stripeWebhookRequestFactory->createFromGlobals();

            $result = $this->checkoutService->handleWebhook(
                $webhookRequest->payload,
                $webhookRequest->signatureHeader,
            );

            $this->json([
                'received' => true,
                'processed' => $result['processed'],
                'eventId' => $result['eventId'],
                'eventType' => $result['eventType'],
            ], 200);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respondJson($error, 400);
        }
    }

    private function getSessionKey(): string
    {
        return $this->sessionService->getSessionId();
    }

    private function getLoggedInUserId(): ?int
    {
        return $this->sessionService->isLoggedIn() ? $this->sessionService->getUserId() : null;
    }

    private function requireAuthenticatedUserId(): int
    {
        $userId = $this->getLoggedInUserId();
        if ($userId === null) {
            throw new \RuntimeException('Please log in to continue checkout.');
        }

        return $userId;
    }
}
