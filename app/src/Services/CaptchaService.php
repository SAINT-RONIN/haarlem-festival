<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Interfaces\ICaptchaService;

/**
 * Google reCAPTCHA v2 verification via the siteverify REST API.
 *
 * Validates reCAPTCHA tokens submitted with public forms (registration, contact).
 * When RECAPTCHA_SITE_KEY or RECAPTCHA_SECRET_KEY is missing, verification is
 * silently bypassed (returns true) so development environments work without keys.
 * In production, verification fails closed: if Google is unreachable the request is rejected.
 *
 * Environment variables:
 *   RECAPTCHA_SITE_KEY   - public key embedded in the frontend widget
 *   RECAPTCHA_SECRET_KEY - private key used for server-side verification
 */
class CaptchaService implements ICaptchaService
{
    private readonly string $siteKey;
    private readonly string $secretKey;
    private readonly string $verifyUrl;
    private readonly string $appEnv;
    private readonly string $appUrl;
    private readonly ?bool $explicitEnabled;

    public function __construct()
    {
        $this->siteKey = getenv('RECAPTCHA_SITE_KEY') ?: '';
        $this->secretKey = getenv('RECAPTCHA_SECRET_KEY') ?: '';
        $this->verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
        $this->appEnv = strtolower((string)(getenv('APP_ENV') ?: 'local'));
        $this->appUrl = (string)(getenv('APP_URL') ?: '');
        $this->explicitEnabled = $this->readExplicitEnabledFlag();
    }

    /**
     * Gets the reCAPTCHA site key for use in frontend.
     */
    public function getSiteKey(): string
    {
        return $this->hasKeys() ? $this->siteKey : '';
    }

    /**
     * Checks if reCAPTCHA is configured (has valid keys).
     */
    public function isEnabled(): bool
    {
        if ($this->explicitEnabled !== null) {
            return $this->explicitEnabled && $this->hasKeys();
        }

        if ($this->isLocalEnvironment()) {
            return false;
        }

        return $this->hasKeys();
    }

    /**
     * Verifies the reCAPTCHA response from the form submission.
     *
     * @param string|null $recaptchaResponse The g-recaptcha-response from POST
     * @param string|null $remoteAddr The client IP address from $_SERVER['REMOTE_ADDR']
     * @return bool True if verification passed, false otherwise
     */
    public function verify(?string $recaptchaResponse, ?string $remoteAddr): bool
    {
        if (!$this->isEnabled()) {
            return true;
        }

        if (empty($recaptchaResponse)) {
            return false;
        }

        $responseData = $this->requestVerification($recaptchaResponse, $remoteAddr);
        if ($responseData === null) {
            error_log('reCAPTCHA verification failed: Could not connect to Google');
            return false;
        }

        return isset($responseData['success']) && $responseData['success'] === true;
    }

    private function hasKeys(): bool
    {
        return $this->siteKey !== '' && $this->secretKey !== '';
    }

    private function isLocalEnvironment(): bool
    {
        if (in_array($this->appEnv, ['local', 'development', 'dev'], true)) {
            return true;
        }

        return $this->isLocalHost($this->appUrl);
    }

    private function isLocalHost(string $url): bool
    {
        if ($url === '') {
            return false;
        }

        $host = parse_url($url, PHP_URL_HOST);
        if (!is_string($host) || $host === '') {
            return false;
        }

        return in_array(strtolower($host), ['localhost', '127.0.0.1'], true);
    }

    private function readExplicitEnabledFlag(): ?bool
    {
        $value = getenv('RECAPTCHA_ENABLED');
        if ($value === false || $value === '') {
            return null;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function requestVerification(?string $recaptchaResponse, ?string $remoteAddr): ?array
    {
        $data = [
            'secret' => $this->secretKey,
            'response' => $recaptchaResponse,
            'remoteip' => $remoteAddr ?? '',
        ];

        $result = $this->sendCurlVerificationRequest($data);
        if ($result === null) {
            $result = $this->sendStreamVerificationRequest($data);
        }

        if ($result === null) {
            return null;
        }

        $decoded = json_decode($result, true);
        return is_array($decoded) ? $decoded : null;
    }

    /**
     * @param array<string, string> $data
     */
    private function sendCurlVerificationRequest(array $data): ?string
    {
        if (!function_exists('curl_init')) {
            return null;
        }

        $handle = curl_init($this->verifyUrl);
        if ($handle === false) {
            return null;
        }

        curl_setopt_array($handle, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ]);

        $result = curl_exec($handle);
        curl_close($handle);

        return is_string($result) ? $result : null;
    }

    /**
     * @param array<string, string> $data
     */
    private function sendStreamVerificationRequest(array $data): ?string
    {
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
                'timeout' => 10,
            ],
        ];

        $context = stream_context_create($options);
        $result = @file_get_contents($this->verifyUrl, false, $context);
        return $result === false ? null : $result;
    }
}
