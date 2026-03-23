<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Interfaces\ICaptchaService;

/**
 * Service for Google reCAPTCHA v2 verification.
 *
 * Validates reCAPTCHA responses from form submissions.
 * Configure your keys via environment variables:
 *   - RECAPTCHA_SITE_KEY: The public site key (used in frontend)
 *   - RECAPTCHA_SECRET_KEY: The private secret key (used for verification)
 *
 * Get your keys at: https://www.google.com/recaptcha/admin
 */
class CaptchaService implements ICaptchaService
{
    private readonly string $siteKey;
    private readonly string $secretKey;
    private readonly string $verifyUrl;

    public function __construct()
    {
        $this->siteKey = getenv('RECAPTCHA_SITE_KEY') ?: '';
        $this->secretKey = getenv('RECAPTCHA_SECRET_KEY') ?: '';
        $this->verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
    }

    /**
     * Gets the reCAPTCHA site key for use in frontend.
     */
    public function getSiteKey(): string
    {
        return $this->siteKey;
    }

    /**
     * Checks if reCAPTCHA is configured (has valid keys).
     */
    public function isEnabled(): bool
    {
        return !empty($this->siteKey) && !empty($this->secretKey);
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
        // If reCAPTCHA is not configured, skip verification (dev mode)
        if (!$this->isEnabled()) {
            return true;
        }

        if (empty($recaptchaResponse)) {
            return false;
        }

        // Send verification request to Google
        $data = [
            'secret' => $this->secretKey,
            'response' => $recaptchaResponse,
            'remoteip' => $remoteAddr ?? '',
        ];

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

        if ($result === false) {
            // If we can't reach Google, log and fail closed (reject)
            error_log('reCAPTCHA verification failed: Could not connect to Google');
            return false;
        }

        $responseData = json_decode($result, true);

        return isset($responseData['success']) && $responseData['success'] === true;
    }

}
