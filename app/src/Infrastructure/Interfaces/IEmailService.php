<?php

declare(strict_types=1);

namespace App\Infrastructure\Interfaces;

/**
 * Contract for transactional email delivery (password resets, order confirmations, etc.).
 * Implementations may send via SMTP, a third-party API, or simply log in development.
 */
interface IEmailService
{
    /**
     * Sends a password reset email containing a one-time reset link.
     *
     * @param string $resetToken The unhashed token that will be embedded in the reset URL
     * @return bool True if the email was delivered (or logged in dev mode)
     * @throws \RuntimeException When SMTP is not configured or sending to local addresses is blocked
     */
    public function sendPasswordResetEmail(string $toEmail, string $resetToken): bool;
}
