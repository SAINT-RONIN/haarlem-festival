<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\DTOs\Session\SessionContext;
use App\Exceptions\CheckoutException;
use App\Http\Requests\Interfaces\IStripeWebhookRequestFactory;
use App\Mappers\CheckoutMapper;
use App\Mappers\ProgramMapper;
use App\Repositories\CheckoutContentRepository;
use App\Services\Interfaces\ICheckoutService;
use App\Services\Interfaces\IProgramService;
use App\Services\Interfaces\ISessionService;

/**
 * Handles the ticket checkout flow: displaying the checkout page, creating Stripe sessions,
 * processing success/cancel callbacks, and receiving Stripe webhook events.
 */
class CheckoutController extends BaseController
{
    public function __construct(
        private readonly IProgramService $programService,
        private readonly CheckoutContentRepository $checkoutContentRepo,
        private readonly ISessionService $sessionService,
        private readonly ICheckoutService $checkoutService,
        private readonly IStripeWebhookRequestFactory $stripeWebhookRequestFactory,
    ) {
    }

    /**
     * Displays the checkout page with the user's program items and CMS content.
     * Redirects to /my-program if the cart is empty.
     * GET /checkout
     */
    public function index(): void
    {
        try {
            $context = $this->resolveSessionContext();

            $programData = $this->programService->getProgramData($context->sessionKey, $context->userId);

            if ($programData->items === []) {
                $this->redirect('/my-program');
                return;
            }

            $cmsContent = $this->checkoutContentRepo->findCheckoutMainContent('checkout', 'main');
            $viewModel = ProgramMapper::toCheckoutViewModel($programData, $cmsContent, $context->isLoggedIn);

            $this->renderView(__DIR__ . '/../Views/pages/checkout.php', $viewModel);
        } catch (CheckoutException $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Creates a Stripe Checkout session and returns the redirect URL as JSON.
     * Requires the user to be authenticated.
     * POST /checkout/create-session
     */
    public function createSession(): void
    {
        try {
            $context = $this->resolveSessionContext();
            $userId = $this->requireAuthenticatedUserId();

            $payload = $this->readJsonBody();
            $programData = $this->programService->getProgramData($context->sessionKey, $userId);
            $result = $this->checkoutService->createCheckoutSession($programData, $userId, $payload);

            $this->json([
                'success' => true,
                'redirectUrl' => $result['redirectUrl'],
            ], 200);
        } catch (CheckoutException|\InvalidArgumentException $error) {
            ControllerErrorResponder::respondJson($error, 400);
        }
    }

    /**
     * Renders the post-payment success page with an order summary from Stripe.
     * GET /checkout/success?session_id=...
     */
    public function success(): void
    {
        try {
            $sessionId = $this->readStringQueryParam('session_id', 255);
            $sessionSummary = $sessionId !== null ? $this->checkoutService->getSessionSummary($sessionId) : null;
            $viewModel = CheckoutMapper::toSuccessViewModel($sessionSummary, $this->resolveSessionContext()->isLoggedIn);

            $this->renderView(__DIR__ . '/../Views/pages/checkout-success.php', $viewModel);
        } catch (CheckoutException $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Handles payment cancellation, marks the order accordingly, and shows the cancel page.
     * GET /checkout/cancel?order_id=...&payment_id=...
     */
    public function cancel(): void
    {
        try {
            $orderId = $this->readPositiveIntQueryParam('order_id');
            $paymentId = $this->readPositiveIntQueryParam('payment_id');

            $cancelResult = $this->checkoutService->handleCancel($orderId, $paymentId);
            $viewModel = CheckoutMapper::toCancelViewModel($cancelResult, $this->resolveSessionContext()->isLoggedIn);

            $this->renderView(__DIR__ . '/../Views/pages/checkout-cancel.php', $viewModel);
        } catch (CheckoutException $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Receives Stripe webhook events (e.g. payment confirmation) and processes them.
     * POST /checkout/webhook
     */
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
        } catch (CheckoutException|\InvalidArgumentException $error) {
            ControllerErrorResponder::respondJson($error, 400);
        }
    }

    /** Builds a SessionContext from the current session state for cart operations. */
    private function resolveSessionContext(): SessionContext
    {
        $sessionKey = $this->sessionService->getSessionId();
        $userId = $this->sessionService->isLoggedIn() ? $this->sessionService->getUserId() : null;

        return new SessionContext(
            sessionKey: $sessionKey,
            userId: $userId,
            isLoggedIn: $userId !== null,
        );
    }

    /**
     * @throws CheckoutException if no user is logged in
     */
    private function requireAuthenticatedUserId(): int
    {
        $context = $this->resolveSessionContext();
        if (!$context->isLoggedIn) {
            throw new CheckoutException('Please log in to continue checkout.');
        }

        return $context->userId;
    }
}
