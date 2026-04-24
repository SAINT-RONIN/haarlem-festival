<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Interfaces\ICaptchaService;

// Google reCAPTCHA v2 verification. Silently bypassed when keys are missing (dev).
// In production, verification fails closed: if Google is unreachable the request is rejected.
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
        $this->appEnv = strtolower((string) (getenv('APP_ENV') ?: 'local'));
        $this->appUrl = (string) (getenv('APP_URL') ?: '');
        $this->explicitEnabled = $this->readExplicitEnabledFlag();
    }

    public function getSiteKey(): string
    {
        return $this->hasKeys() ? $this->siteKey : '';
    }

    public function isEnabled(): bool
    {
        // Priority 1: If the env var explicitly says on or off, follow that (but keys must still exist).
        if ($this->explicitEnabled !== null) {
            return $this->explicitEnabled && $this->hasKeys();
        }

        // Priority 2: If no explicit flag, skip captcha in local/dev environments.
        if ($this->isLocalEnvironment()) {
            return false;
        }

        // Priority 3: All other environments get captcha if keys are configured.
        return $this->hasKeys();
    }

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

    // null = "not set, fall through to default logic"; true/false = explicit override.
    private function readExplicitEnabledFlag(): ?bool
    {
        $value = getenv('RECAPTCHA_ENABLED');
        if ($value === false || $value === '') {
            return null;
        }

        // FILTER_NULL_ON_FAILURE means an unrecognisable value (e.g. "yes" or "1.0") returns
        // null instead of false, so we can distinguish "not set" from "set to false".
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    // Tries cURL first, falls back to PHP streams. Returns null when both fail.
    /** @return array<string, mixed>|null */
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

    /** @param array<string, string> $data */
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

    /** @param array<string, string> $data */
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
        // @ suppresses the PHP warning that file_get_contents emits on network failure — we already handle false.
        $result = @file_get_contents($this->verifyUrl, false, $context);
        return $result === false ? null : $result;
    }
}
