<?php

declare(strict_types=1);

namespace App\Infrastructure\Interfaces;

use App\DTOs\Domain\Invoice\InvoiceEmailMessage;
use App\DTOs\Domain\Tickets\TicketEmailMessage;

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

    /**
     * Sends a paid-order invoice email with the invoice PDF attachment.
     *
     * @throws \App\Exceptions\SmtpNotConfiguredException When SMTP is not configured or local sending is blocked
     * @throws \App\Exceptions\EmailDeliveryException When SMTP delivery fails unexpectedly
     */
    public function sendInvoiceEmail(InvoiceEmailMessage $message): bool;

    /**
     * Sends an email confirmation for account email changes.
     *
     * @param string $toEmail Recipient email address
     * @param string $rawToken The raw token (will be included in URL)
     * @return bool True if sent successfully
     * @throws \App\Exceptions\SmtpNotConfiguredException When SMTP is not configured or local sending is blocked
     * @throws \App\Exceptions\EmailDeliveryException When SMTP delivery fails unexpectedly
     */
    public function sendEmailConfirmationEmail(string $toEmail, string $rawToken): bool;

    /**
     * Sends an account update confirmation email for any account data change.
     *
     * @param string $toEmail Recipient email address
     * @param string $userName User's full name for personalization
     * @param string $changeDescription Description of what was changed (e.g., "password", "email", "profile name", "profile picture")
     * @return bool True if sent successfully
     * @throws \App\Exceptions\SmtpNotConfiguredException When SMTP is not configured or local sending is blocked
     * @throws \App\Exceptions\EmailDeliveryException When SMTP delivery fails unexpectedly
     */
    public function sendAccountUpdateConfirmationEmail(string $toEmail, string $userName, string $changeDescription): bool;
}
