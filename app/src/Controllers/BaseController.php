<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Enums\PriceType;
use App\Enums\TimeRange;
use App\Models\ScheduleFilterParams;
use App\ViewModels\BaseViewModel;

/**
 * Base controller for pages rendered from a single BaseViewModel instance.
 */
abstract class BaseController
{
    protected function renderPage(string $viewPath, BaseViewModel $viewModel): void
    {
        $cms = $viewModel->cms;
        $currentPage = $viewModel->currentPage;
        $includeNav = $viewModel->includeNav;
        $isLoggedIn = $viewModel->globalUi->isLoggedIn;

        require $viewPath;
    }

    protected function renderView(string $viewPath, object $viewModel): void
    {
        require $viewPath;
    }

    protected function redirect(string $location): void
    {
        header('Location: ' . $location);
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

    protected function readRawBody(): string
    {
        return (string)file_get_contents('php://input');
    }

    protected function readStringQueryParam(string $key, int $maxLength = 255): ?string
    {
        if (!isset($_GET[$key])) {
            return null;
        }

        $value = trim((string)$_GET[$key]);
        if ($value === '') {
            return null;
        }

        return mb_substr($value, 0, $maxLength);
    }

    protected function readPositiveIntQueryParam(string $key): ?int
    {
        $value = $this->readStringQueryParam($key, 32);
        if ($value === null || !ctype_digit($value)) {
            return null;
        }

        $intValue = (int)$value;
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

        if ($day === null && $timeRange === null && $priceType === null
            && $venue === null && $language === null && $age === null) {
            return null;
        }

        return $this->buildScheduleFilterParams($day, $timeRange, $priceType, $venue, $language, $age);
    }

    private function buildScheduleFilterParams(
        ?string $day,
        ?string $timeRange,
        ?string $priceType,
        ?string $venue,
        ?string $language,
        ?int $age,
    ): ScheduleFilterParams {
        return new ScheduleFilterParams(
            day: $day !== null ? strtolower($day) : null,
            timeRange: TimeRange::tryFrom($timeRange ?? '') !== null ? $timeRange : null,
            priceType: PriceType::tryFrom($priceType ?? '') !== null ? $priceType : null,
            venue: $venue,
            language: $language !== null ? strtolower($language) : null,
            age: $age,
        );
    }

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
     */
    protected function readJsonBody(): array
    {
        $raw = $this->readRawBody();
        if (trim($raw) === '') {
            throw new \InvalidArgumentException('Missing JSON body.');
        }

        try {
            $body = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $error) {
            throw new \InvalidArgumentException('Invalid JSON body.', 0, $error);
        }

        if (!is_array($body)) {
            throw new \InvalidArgumentException('Invalid JSON body.');
        }

        return $body;
    }
}
