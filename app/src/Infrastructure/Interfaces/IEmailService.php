<?php

declare(strict_types=1);

namespace App\Infrastructure\Interfaces;

use App\DTOs\Tickets\TicketEmailMessage;

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
     * @return bool True if the email was delivered
     * @throws \App\Exceptions\SmtpNotConfiguredException When SMTP is not configured or local sending is blocked
     * @throws \App\Exceptions\EmailDeliveryException When SMTP delivery fails unexpectedly
     */
    public function sendPasswordResetEmail(string $toEmail, string $resetToken): bool;

    /**
     * Sends a paid-order ticket email with one or more PDF attachments.
     *
     * @throws \App\Exceptions\SmtpNotConfiguredException When SMTP is not configured or local sending is blocked
     * @throws \App\Exceptions\EmailDeliveryException When SMTP delivery fails unexpectedly
     */
    public function sendOrderTicketsEmail(TicketEmailMessage $message): bool;
}
