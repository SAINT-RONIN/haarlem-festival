<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\DTOs\Domain\Filters\ScheduleFilterParams;
use App\DTOs\Domain\Session\SessionContext;
use App\Enums\PriceType;
use App\Enums\TimeRange;
use App\Exceptions\JsonBodyParseException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Services\Interfaces\ISessionService;
use App\View\ViewRenderer;
use App\ViewModels\BaseViewModel;

/**
 * Shared foundation for all controllers. Provides view rendering, JSON responses,
 * request body/query-string parsing, and schedule filter extraction.
 */
abstract class BaseController
{
    public function __construct(
        protected readonly ?ISessionService $sessionService = null,
    ) {}

    /** Renders a full page view through the shared isolated PHP renderer. */
    protected function renderPage(string $viewPath, BaseViewModel $viewModel): void
    {
        ViewRenderer::render($viewPath, ['viewModel' => $viewModel]);
    }

    /** Renders a view through the shared isolated PHP renderer. */
    protected function renderView(string $viewPath, object $viewModel): void
    {
        ViewRenderer::render($viewPath, ['viewModel' => $viewModel]);
    }

    protected function redirect(string $location): void
    {
        header('Location: ' . $location);
    }

    protected function redirectAndExit(string $location): never
    {
        $this->redirect($location);
        exit;
    }

    protected function renderNotFoundPage(): void
    {
        http_response_code(404);
        require __DIR__ . '/../Views/pages/errors/404.php';
    }

    /**
     * Wraps a controller action with shared HTML error handling.
     *
     * @param callable(): void $action
     */
    protected function handlePageRequest(callable $action): void
    {
        try {
            $action();
        } catch (NotFoundException) {
            $this->renderNotFoundPage();
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Wraps a controller action with shared JSON error handling.
     *
     * @param callable(): void $action
     * @param array<int, class-string> $badRequestExceptions
     */
    protected function handleJsonRequest(
        callable $action,
        array $badRequestExceptions = [
            \InvalidArgumentException::class,
            JsonBodyParseException::class,
            ValidationException::class,
        ],
    ): void {
        try {
            $action();
        } catch (\Throwable $error) {
            ControllerErrorResponder::respondJson($error, $this->resolveJsonStatusCode($error, $badRequestExceptions));
        }
    }

    /**
     * @param array<string,mixed> $data
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        // Ensure API responses are clean JSON even if something echoed earlier.
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        try {
            echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            http_response_code(500);
            echo '{"success":false,"error":"Failed to encode JSON response."}';
        }

        exit;
    }

    /** Reads the raw HTTP request body (e.g. for webhook signature verification). */
    protected function readRawBody(): string
    {
        return (string) file_get_contents('php://input');
    }

    /** Reads and trims a query-string parameter, truncating to $maxLength. Returns null if absent or empty. */
    protected function readStringQueryParam(string $key, int $maxLength = 255): ?string
    {
        if (!isset($_GET[$key])) {
            return null;
        }

        $value = trim((string) $_GET[$key]);
        if ($value === '') {
            return null;
        }

        return mb_substr($value, 0, $maxLength);
    }

    /** Reads a query-string parameter as a positive integer. Returns null if absent, non-numeric, or zero. */
    protected function readPositiveIntQueryParam(string $key): ?int
    {
        $value = $this->readStringQueryParam($key, 32);
        if ($value === null || !ctype_digit($value)) {
            return null;
        }

        $intValue = (int) $value;
        return $intValue > 0 ? $intValue : null;
    }

    /**
     * Reads schedule filter parameters from the query string.
     * Returns null if no filter params are present (no filtering applied).
     */
    protected function readScheduleFilterParams(): ?ScheduleFilterParams
    {
        $day = $this->readStringQueryParam('day');
        $timeRange = $this->readStringQueryParam('timeRange');
        $priceType = $this->readStringQueryParam('priceType');
        $venue = $this->readStringQueryParam('venue');
        $language = $this->readStringQueryParam('language');
        $age = $this->readPositiveIntQueryParam('age');
        $startTime = $this->readStringQueryParam('startTime');

        if ($day === null && $timeRange === null && $priceType === null
            && $venue === null && $language === null && $age === null && $startTime === null) {
            return null;
        }

        return $this->buildScheduleFilterParams($day, $timeRange, $priceType, $venue, $language, $age, $startTime);
    }

    /** Constructs a ScheduleFilterParams DTO, normalizing enum values and lowercasing free-text fields. */
    private function buildScheduleFilterParams(
        ?string $day,
        ?string $timeRange,
        ?string $priceType,
        ?string $venue,
        ?string $language,
        ?int $age,
        ?string $startTime,
    ): ScheduleFilterParams {
        return new ScheduleFilterParams(
            day: $day !== null ? strtolower($day) : null,
            timeRange: TimeRange::tryFrom($timeRange ?? '')?->value,
            priceType: PriceType::tryFrom($priceType ?? '')?->value,
            venue: $venue,
            language: $language !== null ? strtolower($language) : null,
            age: $age,
            startTime: $startTime,
        );
    }

    /** Reads and trims a POST parameter, truncating to $maxLength. Returns null if absent or empty. */
    protected function readStringPostParam(string $key, int $maxLength = 255): ?string
    {
        if (!isset($_POST[$key])) {
            return null;
        }

        $value = trim((string) $_POST[$key]);
        if ($value === '') {
            return null;
        }

        return mb_substr($value, 0, $maxLength);
    }

    /** Reads a POST parameter as an integer. Returns null if absent, empty, or non-numeric. */
    protected function readOptionalIntPostParam(string $key): ?int
    {
        $value = $this->readStringPostParam($key, 32);
        if ($value === null || !is_numeric($value)) {
            return null;
        }

        return (int) $value;
    }

    /** Reads a POST parameter as a boolean. Returns true if the value is '1' or 'on'. */
    protected function readBoolPostParam(string $key): bool
    {
        $raw = $_POST[$key] ?? '';
        return $raw === '1' || $raw === 'on';
    }

    /** Reads a server/header value from $_SERVER (e.g. HTTP_STRIPE_SIGNATURE). Returns null if absent or empty. */
    protected function readServerHeader(string $headerName): ?string
    {
        $value = $_SERVER[$headerName] ?? null;
        if (!is_string($value)) {
            return null;
        }

        $value = trim($value);
        return $value === '' ? null : $value;
    }

    /**
     * @return array<string,mixed>
     * @throws \InvalidArgumentException if the body is missing, unparseable, or not a JSON object
     */
    protected function readJsonBody(): array
    {
        $raw = $this->readRawBody();
        if (trim($raw) === '') {
            throw new JsonBodyParseException('Missing JSON body.');
        }

        try {
            $body = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $error) {
            throw new JsonBodyParseException('Invalid JSON body.', 0, $error);
        }

        if (!is_array($body)) {
            throw new JsonBodyParseException('Invalid JSON body.');
        }

        return $body;
    }

    protected function isLoggedIn(): bool
    {
        return $this->requireSessionService()->isLoggedIn();
    }

    /** Builds a SessionContext from the given session service for cart and program operations. */
    protected function resolveSessionContext(?ISessionService $sessionService = null): SessionContext
    {
        $sessionService ??= $this->requireSessionService();
        $sessionKey = $sessionService->getSessionId();
        $isLoggedIn = $sessionService->isLoggedIn();
        $userId = $isLoggedIn ? $sessionService->getUserId() : null;

        return new SessionContext(
            sessionKey: $sessionKey,
            userId: $userId,
            isLoggedIn: $isLoggedIn,
        );
    }

    protected function requireSessionService(): ISessionService
    {
        if ($this->sessionService === null) {
            throw new \LogicException('This controller requires a session service.');
        }

        return $this->sessionService;
    }

    /**
     * @param array<int, class-string> $badRequestExceptions
     */
    private function resolveJsonStatusCode(\Throwable $error, array $badRequestExceptions): int
    {
        foreach ($badRequestExceptions as $exceptionClass) {
            if ($error instanceof $exceptionClass) {
                return 400;
            }
        }

        return $error instanceof NotFoundException ? 404 : 500;
    }
}
