<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\CheckoutException;
use App\Exceptions\CheckoutInputException;
use App\Exceptions\RetryPaymentException;
use App\Exceptions\StripeWebhookException;
use App\Helpers\AssetVersionHelper;
use App\Http\Requests\Interfaces\IStripeWebhookRequestFactory;
use App\Mappers\CheckoutMapper;
use App\Mappers\CheckoutRetryMapper;
use App\Mappers\ProgramMapper;
use App\Services\Interfaces\ICheckoutService;
use App\Services\Interfaces\IProgramService;
use App\Services\Interfaces\ISessionService;
use App\Services\Interfaces\IStripeWebhookHandler;

/**
 * Handles the ticket checkout flow: displaying the checkout page, creating Stripe sessions,
 * processing success/cancel callbacks, and receiving Stripe webhook events.
 */
class CheckoutController extends BaseController
{
    public function __construct(
        private readonly IProgramService $programService,
        ISessionService $sessionService,
        private readonly ICheckoutService $checkoutService,
        private readonly IStripeWebhookHandler $stripeWebhookHandler,
        private readonly IStripeWebhookRequestFactory $stripeWebhookRequestFactory,
    ) {
        parent::__construct($sessionService);
    }

    /**
     * Displays the checkout page with the user's program items and CMS content.
     * Redirects to /my-program if the cart is empty.
     * GET /checkout
     */
    public function index(): void
    {
        $this->handlePageRequest(function (): void {
            $this->renderCheckoutPage();
        });
    }

    /** Loads session context, program data, CMS content, and renders the checkout page. */
    private function renderCheckoutPage(): void
    {
        $context = $this->resolveSessionContext();
        $programData = $this->programService->getProgramData($context->sessionKey, $context->userId);

        if ($programData->items === []) {
            $this->redirectAndExit('/my-program');
            return;
        }

        $cmsContent = $this->checkoutService->getCheckoutMainContent();
        $jsVersion = AssetVersionHelper::resolveJsVersion(__DIR__ . '/../../public/assets/js/checkout.js');
        $viewModel = ProgramMapper::toCheckoutViewModel($programData, $cmsContent, $context->isLoggedIn, $jsVersion);

        $this->renderView(__DIR__ . '/../Views/pages/checkout.php', $viewModel);
    }

    /**
     * Creates a Stripe Checkout session and returns the redirect URL as JSON.
     * Requires the user to be authenticated.
     * POST /checkout/create-session
     */
    public function createSession(): void
    {
        $this->handleJsonRequest(function (): void {
            $this->processCreateSession();
        }, [CheckoutInputException::class, \InvalidArgumentException::class]);
    }

    /** Resolves auth context, reads JSON payload, creates a Stripe session, and returns the redirect URL. */
    private function processCreateSession(): void
    {
        $context = $this->resolveSessionContext();
        $userId = $this->requireAuthenticatedUserId();

        $payload = $this->readJsonBody();
        $programData = $this->programService->getProgramData($context->sessionKey, $userId);
        $result = $this->checkoutService->createCheckoutSession($programData, $userId, $payload);

        $this->json([
            'success' => true,
            'redirectUrl' => $result->redirectUrl,
        ], 200);
    }

    /**
     * Renders the post-payment success page with an order summary from Stripe.
     * GET /checkout/success?session_id=...
     */
    public function success(): void
    {
        $this->handlePageRequest(function (): void {
            $sessionId = $this->readStringQueryParam('session_id', 255);
            $sessionSummary = $sessionId !== null ? $this->checkoutService->getSessionSummary($sessionId) : null;
            $viewModel = CheckoutMapper::toSuccessViewModel($sessionSummary, $this->resolveSessionContext()->isLoggedIn);

            $this->renderView(__DIR__ . '/../Views/pages/checkout-success.php', $viewModel);
        });
    }

    /**
     * Handles payment cancellation, marks the order accordingly, and shows the cancel page.
     * GET /checkout/cancel?order_id=...&payment_id=...
     */
    public function cancel(): void
    {
        $this->handlePageRequest(function (): void {
            $orderId = $this->readPositiveIntQueryParam('order_id');
            $paymentId = $this->readPositiveIntQueryParam('payment_id');

            $cancelResult = $this->checkoutService->handleCancel($orderId, $paymentId);
            $viewModel = CheckoutMapper::toCancelViewModel($cancelResult, $this->resolveSessionContext()->isLoggedIn);

            $this->renderView(__DIR__ . '/../Views/pages/checkout-cancel.php', $viewModel);
        });
    }

    /**
     * Displays the retry payment page for a pending order.
     * GET /checkout/retry/{orderId}
     */
    public function retryIndex(int $orderId): void
    {
        $this->handlePageRequest(function () use ($orderId): void {
            $userId = $this->requireAuthenticatedUserId();
            $order = $this->checkoutService->getRetryOrder($orderId, $userId);
            $viewModel = CheckoutRetryMapper::toRetryViewModel($order, true);

            $this->renderView(__DIR__ . '/../Views/pages/checkout-retry.php', $viewModel);
        });
    }

    /**
     * Creates a new Stripe session for an existing pending order.
     * POST /api/checkout/retry-session
     */
    public function retrySession(): void
    {
        $this->handleJsonRequest(function (): void {
            $userId = $this->requireAuthenticatedUserId();
            $payload = $this->readJsonBody();
            $orderId = (int) ($payload['orderId'] ?? 0);

            $result = $this->checkoutService->retryCheckoutSession($orderId, $userId, $payload);

            $this->json([
                'success' => true,
                'redirectUrl' => $result->redirectUrl,
            ]);
        }, [RetryPaymentException::class, CheckoutInputException::class, \InvalidArgumentException::class]);
    }

    /**
     * Receives Stripe webhook events (e.g. payment confirmation) and processes them.
     * POST /checkout/webhook
     */
    public function webhook(): void
    {
        $this->handleJsonRequest(function (): void {
            $this->processWebhook();
        }, [StripeWebhookException::class, \InvalidArgumentException::class]);
    }

    /** Creates a webhook request from globals, processes it, and returns the result as JSON. */
    private function processWebhook(): void
    {
        $webhookRequest = $this->stripeWebhookRequestFactory->createFromGlobals();

        $result = $this->stripeWebhookHandler->handleWebhook(
            $webhookRequest->payload,
            $webhookRequest->signatureHeader,
        );

        $this->json([
            'received' => true,
            'processed' => $result->processed,
            'eventId' => $result->eventId,
            'eventType' => $result->eventType,
        ], 200);
    }

    /**
     * @throws CheckoutException if no user is logged in
     */
    private function requireAuthenticatedUserId(): int
    {
        $context = $this->resolveSessionContext();
        if (!$context->isLoggedIn) {
            throw new CheckoutInputException('Please log in to continue checkout.');
        }

        return $context->userId;
    }
}
