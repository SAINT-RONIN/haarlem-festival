<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\DTOs\Domain\Invoice\InvoiceEmailMessage;
use App\DTOs\Domain\Tickets\TicketEmailAttachment;
use App\DTOs\Domain\Tickets\TicketEmailMessage;
use App\Exceptions\EmailDeliveryException;
use App\Exceptions\SmtpNotConfiguredException;
use App\Infrastructure\Interfaces\IEmailService;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Service for sending emails via SMTP.
 *
 * Configure via environment variables:
 *   MAIL_HOST     - SMTP server (e.g., smtp.gmail.com)
 *   MAIL_PORT     - SMTP port (587 for TLS, 465 for SSL)
 *   MAIL_USERNAME - SMTP username/email
 *   MAIL_PASSWORD - SMTP password or app password (for Gmail: App Password, not your normal password)
 *   MAIL_FROM_ADDRESS - From email address
 *   MAIL_FROM_NAME    - From name
 *   MAIL_ENCRYPTION   - tls, ssl, or empty (optional, defaults based on port)
 *   MAIL_FORCE_SEND   - true/false, if true then send emails even in local environment (optional)
 *   APP_URL       - Application URL for links
 *   APP_ENV       - Environment (local/production)
 */
class EmailService implements IEmailService
{
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private string $fromAddress;
    private string $fromName;
    private string $appUrl;
    private string $appEnv;
    private bool $forceSend;
    private string $encryption;

    public function __construct()
    {
        $this->host = getenv('MAIL_HOST') ?: '';
        $this->port = (int) (getenv('MAIL_PORT') ?: 587);
        $this->username = getenv('MAIL_USERNAME') ?: '';
        $this->password = getenv('MAIL_PASSWORD') ?: '';
        $this->fromAddress = getenv('MAIL_FROM_ADDRESS') ?: 'noreply@haarlemfestival.nl';
        $this->fromName = getenv('MAIL_FROM_NAME') ?: 'Haarlem Festival';
        $this->appUrl = rtrim(getenv('APP_URL') ?: 'http://localhost', '/');
        $this->appEnv = getenv('APP_ENV') ?: 'local';
        $this->forceSend = filter_var(getenv('MAIL_FORCE_SEND') ?: 'false', FILTER_VALIDATE_BOOLEAN);
        $this->encryption = strtolower(getenv('MAIL_ENCRYPTION') ?: '');
    }

    /**
     * Sends a password reset email with the reset link.
     *
     * @param string $toEmail Recipient email address
     * @param string $rawToken The raw token (will be included in URL)
     * @return bool True if sent successfully
     */
    public function sendPasswordResetEmail(string $toEmail, string $rawToken): bool
    {
        $resetUrl = $this->appUrl . '/reset-password?token=' . urlencode($rawToken);

        $subject = 'Reset Your Password - Haarlem Festival';
        $body = $this->buildResetEmailBody($resetUrl);

        return $this->send($toEmail, $subject, $body);
    }


    public function sendOrderTicketsEmail(TicketEmailMessage $message): bool
    {
        $subject = 'Your Haarlem Festival Tickets - ' . $message->orderReference;
        $body = $this->buildOrderTicketsEmailBody($message);

        return $this->send($message->toEmail, $subject, $body, $message->attachments);
    }

    public function sendInvoiceEmail(InvoiceEmailMessage $message): bool
    {
        $subject = 'Your Haarlem Festival Invoice - ' . $message->orderNumber;
        $body = $this->buildInvoiceEmailBody($message);

        return $this->send($message->recipientEmail, $subject, $body, $message->attachments);
    }

    public function sendAccountUpdateConfirmationEmail(string $toEmail, string $userName, string $changeDescription): bool
    {
        $subject = 'Your Account Has Been Updated - Haarlem Festival';
        $body = $this->buildAccountUpdateConfirmationBody($userName, $changeDescription);

        return $this->send($toEmail, $subject, $body);
    }

    /**
     * Builds the password reset email body.
     */
    private function buildResetEmailBody(string $resetUrl): string
    {
        return <<<EMAIL
            Hello,

            You requested a password reset for your Haarlem Festival account.

            Click the link below to reset your password:
            {$resetUrl}

            This link will expire in 1 hour.

            If you did not request this reset, you can safely ignore this email.

            Best regards,
            Haarlem Festival Team
            EMAIL;
    }

    private function buildAccountUpdateConfirmationBody(string $userName, string $changeDescription): string
    {
        $displayName = $userName !== '' ? $userName : 'valued customer';

        return <<<EMAIL
            Hello {$displayName},

            We are writing to confirm that your {$changeDescription} for the Haarlem Festival account has been successfully changed.

            If you did not make this change or believe your account has been compromised, please contact us immediately at support@haarlemfestival.nl.

            Best regards,
            Haarlem Festival Team
            EMAIL;
    }

    private function buildInvoiceEmailBody(InvoiceEmailMessage $message): string
    {
        $recipientName = $message->recipientName !== '' ? $message->recipientName : 'valued customer';

        return <<<EMAIL
            Hello {$recipientName},

            Please find attached the invoice for your Haarlem Festival order.

            Invoice number: {$message->invoiceNumber}
            Order reference: {$message->orderNumber}

            If you have any questions about this invoice, please visit {$this->appUrl}/my-program.

            Best regards,
            Haarlem Festival Team
            EMAIL;
    }

    private function buildOrderTicketsEmailBody(TicketEmailMessage $message): string
    {
        $recipientName = $message->recipientName !== '' ? $message->recipientName : 'festival guest';
        $summaryLines = $message->eventSummaryLines === []
            ? '- Your tickets are attached as PDF files.'
            : implode("\n", array_map(static fn(string $line) => '- ' . $line, $message->eventSummaryLines));

        return <<<EMAIL
            Hello {$recipientName},

            Your Haarlem Festival payment was successful.

            Order reference: {$message->orderReference}
            Tickets attached: {$message->ticketCount}

            Order summary:
            {$summaryLines}

            How to use your tickets:
            - Open the attached PDF tickets before arriving at the venue.
            - Present each QR code at the entrance for scanning.
            - Each QR code can be scanned only once.

            Keep this email for your records. If you need help, visit {$this->appUrl}/my-program.

            Best regards,
            Haarlem Festival Team
            EMAIL;
    }

    /**
     * Guards against accidental email delivery in development, then dispatches via SMTP.
     *
     * @throws \RuntimeException When SMTP is not configured or local sending is blocked
     */
    public function send(string $to, string $subject, string $body, array $attachments = []): bool
    {
        if (!$this->isSmtpConfigured()) {
            throw new SmtpNotConfiguredException("SMTP not configured. Email to {$to} with subject '{$subject}' was not sent.");
        }

        if ($this->isLocalEnvironment() && !$this->forceSend) {
            throw new SmtpNotConfiguredException("Cannot send mail to local email address '{$to}'.");
        }

        return $this->sendViaSmtp($to, $subject, $body, $attachments);
    }

    /**
     * Checks if SMTP is properly configured.
     */
    private function isSmtpConfigured(): bool
    {
        return $this->host !== '' && $this->username !== '' && $this->password !== '';
    }

    /**
     * Checks if running in local/development environment.
     */
    private function isLocalEnvironment(): bool
    {
        return in_array($this->appEnv, ['local', 'development', 'dev'], true);
    }

    /**
     * Sends email via SMTP using PHPMailer.
     */
    private function sendViaSmtp(string $to, string $subject, string $body, array $attachments = []): bool
    {
        $mail = new PHPMailer(false);
        $mail->isSMTP();
        $mail->Host = $this->host;
        $mail->SMTPAuth = true;
        $mail->Username = $this->username;
        $mail->Password = $this->password;
        $mail->SMTPSecure = $this->resolveEncryption();
        $mail->Port = $this->port;

        $mail->setFrom($this->fromAddress, $this->fromName);
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->isHTML(false);

        foreach ($attachments as $attachment) {
            if ($attachment instanceof TicketEmailAttachment) {
                $mail->addAttachment($attachment->absolutePath, $attachment->displayName);
            } elseif (is_object($attachment) && isset($attachment->absolutePath, $attachment->displayName)) {
                $mail->addAttachment($attachment->absolutePath, $attachment->displayName);
            }
        }

        if (!$mail->send()) {
            throw new EmailDeliveryException('Email sending failed: ' . $mail->ErrorInfo);
        }

        return true;
    }

    /**
     * Resolves SMTP encryption: honours explicit MAIL_ENCRYPTION env var,
     * otherwise infers from port (465 = SMTPS, anything else = STARTTLS).
     */
    private function resolveEncryption(): string
    {
        if ($this->encryption === 'ssl') {
            return PHPMailer::ENCRYPTION_SMTPS;
        }

        if ($this->encryption === 'tls') {
            return PHPMailer::ENCRYPTION_STARTTLS;
        }

        return $this->port === 465
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;
    }
}
