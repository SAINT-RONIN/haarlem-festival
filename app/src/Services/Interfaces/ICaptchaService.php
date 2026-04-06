<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

/**
 * Contract for CAPTCHA verification on public forms (registration, contact, etc.).
 * When keys are not configured (development), verification is silently bypassed.
 */
interface ICaptchaService
{
    /**
     * Verifies a reCAPTCHA response.
     *
     * @param string|null $response The reCAPTCHA response token
     * @param string|null $remoteAddr The client IP address from $_SERVER['REMOTE_ADDR']
     * @return bool True if valid
     */
    public function verify(?string $response, ?string $remoteAddr): bool;

    /**
     * Gets the reCAPTCHA site key for frontend use.
     *
     * @return string Site key
     */
    public function getSiteKey(): string;

    /**
     * Returns whether CAPTCHA verification is enabled (i.e., keys are configured).
     */
    public function isEnabled(): bool;
}
