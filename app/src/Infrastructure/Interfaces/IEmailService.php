<?php

declare(strict_types=1);

namespace App\Infrastructure\Interfaces;

/**
 * Interface for Email service.
 */
interface IEmailService
{
    /**
     * Sends a password reset email.
     *
     * @param string $toEmail Recipient email
     * @param string $resetToken Raw reset token
     * @return bool Success status
     */
    public function sendPasswordResetEmail(string $toEmail, string $resetToken): bool;
}
